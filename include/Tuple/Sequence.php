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

namespace Bitfinex\Data\Tuple;

use Bitfinex\Data\Serializer\CsvSerializer;
use Bitfinex\Data\Serializer\SerializerFactory;
use Bitfinex\Data\Serializer\SerializerInterface;

/**
 * The sequence.
 */
class Sequence extends \ArrayIterator implements TupleInterface {

  /**
   * The sequence serializer.
   *
   * @var SerializerInterface
   */
  protected $serializer;

  /**
   * The sequence tuple format name.
   *
   * @var string
   */
  const FORMAT_NAME = self::FORMAT_GROUP . '_sequence';

  /**
   * Construct a sequence.
   *
   * @param array $collection
   *   An ordered list used to initialize the sequence.
   * @param SerializerInterface $serializer
   *   The sequence serializer (default: CSV).
   *
   * @return self
   */
  public function __construct(array $collection = [], SerializerInterface $serializer = NULL) {
    parent::__construct($collection);

    if ($serializer instanceof SerializerInterface) {
      $this->{'serializer'} = $serializer;
    }
    else {
      $this->{'serializer'} = (new SerializerFactory())->create(CsvSerializer::getFormatName());
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
  public function add($item, $offset = NULL) : TupleInterface {
    if ($offset === NULL) {
      $this->append($item);
    }
    else {
      $this->offsetSet($offset, $item);
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function addRange(array $collection) : TupleInterface {
    foreach ($collection as $offset => $item) {
      $this->add($item, $offset);
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function clear() : TupleInterface {
    $this->rewind();

    while ($this->valid()) {
      $this->offsetUnset($this->key());
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function serialize() : string {
    if (($payload = $this->{'serializer'}->serialize($this->getArrayCopy())) === FALSE) {
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
