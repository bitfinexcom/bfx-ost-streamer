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

use Bitfinex\Data\Encoder\Utf8Encoder;
use Bitfinex\Data\Encoder\EncoderInterface;
use Bitfinex\Data\Validator\Strings\NotBlankValidator;
use Bitfinex\Data\Streamer\osTicket\Configuration\Helper;
use Bitfinex\Data\Encoder\EncoderFactory as UnbindedEncoderFactory;
use Bitfinex\Data\Streamer\osTicket\Actions\Formats\EncoderFormatsAlterSignal;

/**
 * The osTicket encoder factory.
 */
class EncoderFactory extends UnbindedEncoderFactory {

  /**
   * The format of the entity that uses the encoder.
   *
   * @var string
   */
  protected $format;

  /**
   * The use case in which the encoder is used.
   *
   * @var string
   */
  protected $context;

  /**
   * Construct the osTicket encoder factory.
   *
   * @param string $context
   *   The use case in which the encoder is used.
   * @param string $format
   *   The format of the entity that uses the encoder.
   *
   * @return self
   */
  public function __construct(string $context, string $format) {
    parent::__construct(new EncoderFormatsAlterSignal());

    if (NotBlankValidator::isNotValid($context) || NotBlankValidator::isNotValid($format)) {
      throw new \InvalidArgumentException();
    }

    $this->{'format'} = $format;
    $this->{'context'} = $context;
  }

  /**
   * {@inheritDoc}
   */
  protected function options() : array {
    $options = parent::options();

    $options[Utf8Encoder::getFormatName()][static::INITIALIZER_KEY] = [
      $this,
      'createUtf8Encoder',
    ];

    return $options;
  }

  /**
   * Create an UTF-8 encoder.
   *
   * @param mixed ...$arguments
   *   A list of arguments suitable for the UTF-8 encoder constructor. If
   *   an argument is not provided, the Osticket configuration will be used.
   *
   * @return EncoderInterface
   *   A new instance of the UTF-8 encoder.
   */
  protected function createUtf8Encoder(...$arguments) : EncoderInterface {
    if (\array_key_exists(0, $arguments) === FALSE) {
      $arguments[0] = Helper::getUtf8EncoderSourceEncondig($this->{'context'}, $this->{'format'});
    }

    return new Utf8Encoder(...$arguments);
  }

}
