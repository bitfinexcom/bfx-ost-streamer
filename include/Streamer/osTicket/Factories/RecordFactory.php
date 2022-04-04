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

namespace Bitfinex\Data\Streamer\osTicket\Factories;

use Bitfinex\Data\Record\KinesisRecord;
use Bitfinex\Data\Record\RecordInterface;
use Bitfinex\Data\Validator\Strings\NotBlankValidator;
use Bitfinex\Data\Streamer\osTicket\Configuration\Helper;
use Bitfinex\Data\Record\RecordFactory as UnbindedRecordFactory;
use Bitfinex\Data\Streamer\osTicket\Actions\Formats\RecordFormatsAlterSignal;

/**
 * The osTicket record factory.
 */
class RecordFactory extends UnbindedRecordFactory {

  /**
   * The use case in which the record is used.
   *
   * @var string
   */
  protected $context;

  /**
   * Construct the osTicket record factory.
   *
   * @param string $context
   *   The use case in which the record is used.
   *
   * @return self
   */
  public function __construct(string $context) {
    parent::__construct(new RecordFormatsAlterSignal());

    if (NotBlankValidator::isNotValid($context)) {
      throw new \InvalidArgumentException();
    }

    $this->{'context'} = $context;
  }

  /**
   * {@inheritDoc}
   */
  protected function options() : array {
    $options = parent::options();

    $options[KinesisRecord::getFormatName()][static::INITIALIZER_KEY] = [
      $this,
      'createKinesisRecord',
    ];

    return $options;
  }

  /**
   * Create a Kinesis record.
   *
   * @param mixed ...$arguments
   *   A list of arguments suitable for the Kinesis record constructor. If
   *   an argument is not provided, the Osticket configuration will be used.
   *
   * @return RecordInterface
   *   A new instance of the Kinesis record.
   */
  protected function createKinesisRecord(...$arguments) : RecordInterface {
    if (\array_key_exists(0, $arguments) === FALSE) {
      $arguments[0] = Helper::getKinesisRecordStreamName($this->{'context'});
    }

    if (\array_key_exists(1, $arguments) === FALSE) {
      $factory = new EncoderFactory($this->{'context'}, KinesisRecord::getFormatName());
      $arguments[1] = $factory->create(Helper::getKinesisRecordEncoderFormat($this->{'context'}));
    }

    if (\array_key_exists(2, $arguments) === FALSE) {
      $factory = new SerializerFactory($this->{'context'}, KinesisRecord::getFormatName());
      $arguments[2] = $factory->create(Helper::getKinesisRecordSerializerFormat($this->{'context'}));
    }

    return new KinesisRecord(...$arguments);
  }

}
