<?php

/**
 * This file is part of bfx-ost-streamer.
 *
 * Copyright (C) 2021-2023 Davide Scola <davide@bitfinex.com>,
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

use Bitfinex\Data\Serializer\CsvSerializer;
use Bitfinex\Data\Serializer\JsonSerializer;
use Bitfinex\Data\Serializer\SerializerInterface;
use Bitfinex\Data\Validator\Strings\NotBlankValidator;
use Bitfinex\Data\Serializer\LineDelimitedJsonSerializer;
use Bitfinex\Data\Streamer\osTicket\Configuration\Helper;
use Bitfinex\Data\Serializer\SerializerFactory as UnbindedSerializerFactory;
use Bitfinex\Data\Streamer\osTicket\Actions\Formats\SerializerFormatsAlterSignal;

/**
 * The osTicket serializer factory.
 */
class SerializerFactory extends UnbindedSerializerFactory {

  /**
   * The format of the entity that uses the serializer.
   *
   * @var string
   */
  protected $format;

  /**
   * The use case in which the serializer is used.
   *
   * @var string
   */
  protected $context;

  /**
   * Construct the osTicket serializer factory.
   *
   * @param string $context
   *   The use case in which the serializer is used.
   * @param string $format
   *   The format of the entity that uses the serializer.
   *
   * @return self
   */
  public function __construct(string $context, string $format) {
    parent::__construct(new SerializerFormatsAlterSignal());

    if (NotBlankValidator::isNotValid($context) || NotBlankValidator::isNotValid($format)) {
      throw new \InvalidArgumentException();
    }

    $this->{'format'} = $format;
    $this->{'context'} = $context;
  }

  /**
   * {@inheritDoc}
   */
  protected function mapping($context = NULL) : array {
    if (isset($context) === FALSE) {
      $context = [
        'format' => $this->{'format'},
        'context' => $this->{'context'},
      ];
    }

    return parent::mapping($context);
  }

  /**
   * {@inheritDoc}
   */
  protected function options() : array {
    $options = parent::options();

    $options[CsvSerializer::getFormatName()][static::INITIALIZER_KEY] = [
      $this,
      'createCsvSerializer',
    ];

    $options[JsonSerializer::getFormatName()][static::INITIALIZER_KEY] = [
      $this,
      'createJsonSerializer',
    ];

    $options[LineDelimitedJsonSerializer::getFormatName()][static::INITIALIZER_KEY] = [
      $this,
      'createLineDelimitedJsonSerializer',
    ];

    return $options;
  }

  /**
   * Create a CSV serializer.
   *
   * @param mixed ...$arguments
   *   A list of arguments suitable for the CSV serializer constructor. If
   *   an argument is not provided, the Osticket configuration will be used.
   *
   * @return SerializerInterface
   *   A new instance of the CSV serializer.
   */
  protected function createCsvSerializer(...$arguments) : SerializerInterface {
    if (\array_key_exists(0, $arguments) === FALSE) {
      $arguments[0] = Helper::getCsvSerializerFieldSeparator($this->{'context'}, $this->{'format'});
    }

    if (\array_key_exists(1, $arguments) === FALSE) {
      $arguments[1] = Helper::getCsvSerializerFieldEnclosure($this->{'context'}, $this->{'format'});
    }

    if (\array_key_exists(2, $arguments) === FALSE) {
      $arguments[2] = Helper::getCsvSerializerEscapeCharacter($this->{'context'}, $this->{'format'});
    }

    if (\array_key_exists(3, $arguments) === FALSE) {
      $arguments[3] = Helper::getCsvSerializerMemoryLimit($this->{'context'}, $this->{'format'});
    }

    return new CsvSerializer(...$arguments);
  }

  /**
   * Create a JSON serializer.
   *
   * @param mixed ...$arguments
   *   A list of arguments suitable for the JSON serializer constructor. If
   *   an argument is not provided, the Osticket configuration will be used.
   *
   * @return SerializerInterface
   *   A new instance of the JSON serializer.
   */
  protected function createJsonSerializer(...$arguments) : SerializerInterface {
    if (\array_key_exists(0, $arguments) === FALSE) {
      $arguments[0] = Helper::getJsonSerializerFlags($this->{'context'}, $this->{'format'});
    }

    if (\array_key_exists(1, $arguments) === FALSE) {
      $arguments[1] = Helper::getJsonSerializerDepth($this->{'context'}, $this->{'format'});
    }

    return new JsonSerializer(...$arguments);
  }

  /**
   * Create a NDJSON serializer.
   *
   * @param mixed ...$arguments
   *   A list of arguments suitable for the NDJSON serializer constructor. If
   *   an argument is not provided, the Osticket configuration will be used.
   *
   * @return SerializerInterface
   *   A new instance of the NDJSON serializer.
   */
  protected function createLineDelimitedJsonSerializer(...$arguments) : SerializerInterface {
    if (\array_key_exists(0, $arguments) === FALSE) {
      $arguments[0] = Helper::getLineDelimitedJsonSerializerLineEnding($this->{'context'}, $this->{'format'});
    }

    if (\array_key_exists(1, $arguments) === FALSE) {
      $arguments[1] = Helper::getJsonSerializerFlags($this->{'context'}, $this->{'format'});
    }

    if (\array_key_exists(2, $arguments) === FALSE) {
      $arguments[2] = Helper::getJsonSerializerDepth($this->{'context'}, $this->{'format'});
    }

    return new LineDelimitedJsonSerializer(...$arguments);
  }

}
