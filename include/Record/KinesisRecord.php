<?php

/**
 * This file is part of bfx-ost-streamer.
 *
 * Copyright (C) 2021-2022 Davide Scola <davide@bitfinex.com>,
 *                         Nicoletta Maia <nicoletta@bitfinex.com>.
 *
 * Licensed under the Apache License,  Version 2.0 (the "License"); you may
 * not use this file except in  compliance with the License. You may obtain
 * a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless  required by  applicable law  or agreed  to in  writing, software
 * distributed  under the  License  is  distributed on  an  "AS IS"  BASIS,
 * WITHOUT  WARRANTIES  OR  CONDITIONS  OF  ANY  KIND,  either  express  or
 * implied. See the License for the specific language governing permissions
 * and limitations under the License.
 */

namespace Bitfinex\Data\Record;

use Bitfinex\Data\Encoder\Utf8Encoder;
use Bitfinex\Data\Tuple\TupleInterface;
use Bitfinex\Data\Encoder\EncoderFactory;
use Bitfinex\Data\Encoder\EncoderInterface;
use Bitfinex\Data\Serializer\JsonSerializer;
use Bitfinex\Data\Serializer\SerializerFactory;
use Bitfinex\Data\Serializer\SerializerInterface;
use Bitfinex\Data\Validator\Strings\KinesisStreamValidator;

/**
 * The Kinesis record.
 *
 * This implementation is suitable for small Kinesis streams that do not
 * use multiple shards.
 */
class KinesisRecord implements RecordInterface {

  /**
   * The internal representation of a Kinesis record.
   *
   * @var array
   */
  protected $record;

  /**
   * The encoder used to encode the data blob.
   *
   * @var EncoderInterface
   */
  protected $encoder;

  /**
   * The serializer used to serialize the record itself.
   *
   * @var SerializerInterface
   */
  protected $serializer;

  /**
   * The Kinesis record format name.
   *
   * @var string
   */
  const FORMAT_NAME = self::FORMAT_GROUP . '_kinesis';

  /**
   * Construct a Kinesis record.
   *
   * @param string $stream
   *   The name of the stream to put the data record into.
   * @param EncoderInterface $encoder
   *   The Kinesis record encoder (default: UTF-8).
   * @param SerializerInterface $serializer
   *   The Kinesis record serializer (default: JSON).
   *
   * @return self
   */
  public function __construct(
    string $stream,
    EncoderInterface $encoder = NULL,
    SerializerInterface $serializer = NULL
  ) {
    if (KinesisStreamValidator::isNotValid($stream)) {
      throw new \InvalidArgumentException();
    }

    $this->{'record'} = [
      'Data' => NULL,
      'StreamName' => $stream,
      'PartitionKey' => \str_replace(__NAMESPACE__, '', __CLASS__),
    ];

    if ($encoder instanceof EncoderInterface) {
      $this->{'encoder'} = $encoder;
    }
    else {
      $this->{'encoder'} = (new EncoderFactory())->create(Utf8Encoder::getFormatName());
    }

    if ($serializer instanceof SerializerInterface) {
      $this->{'serializer'} = $serializer;
    }
    else {
      $this->{'serializer'} = (new SerializerFactory())->create(JsonSerializer::getFormatName());
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getSerializer() : SerializerInterface {
    return $this->{'serializer'};
  }

  /**
   * {@inheritDoc}
   */
  public function setSerializer(SerializerInterface $serializer) {
    $this->{'serializer'} = $serializer;
  }

  /**
   * {@inheritDoc}
   */
  public function update(TupleInterface $collection) : RecordInterface {
    if (($payload = $this->{'encoder'}->encode($collection->serialize())) === FALSE) {
      throw new \UnexpectedValueException();
    }

    $this->{'record'}['Data'] = $payload;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function serialize() : string {
    if (($payload = $this->{'serializer'}->serialize($this->{'record'})) === FALSE) {
      throw new \UnexpectedValueException();
    }

    return $payload;
  }

  /**
   * {@inheritDoc}
   */
  public function unserialize($serialized) {
    throw new \BadMethodCallException('Not implemented');
  }

  /**
   * {@inheritDoc}
   */
  public static function getFormatName() : string {
    return static::FORMAT_NAME;
  }

}
