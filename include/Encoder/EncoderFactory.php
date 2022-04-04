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

namespace Bitfinex\Data\Encoder;

use Bitfinex\Data\AbstractFactory;

/**
 * The encoder factory.
 */
class EncoderFactory extends AbstractFactory {

  /**
   * {@inheritDoc}
   *
   * @return EncoderInterface
   *   A new instance of the encoder.
   */
  public function create(string $format, ...$arguments) : EncoderInterface {
    return parent::create($format, ...$arguments);
  }

  /**
   * {@inheritDoc}
   */
  protected function options() : array {
    return [
      Utf8Encoder::getFormatName() => [
        static::DESCRIPTION_KEY => 'UTF-8',
        static::INITIALIZER_KEY => function (...$arguments) {
          return new Utf8Encoder(...$arguments);
        },
      ],
      HexEncoder::getFormatName() => [
        static::DESCRIPTION_KEY => 'Base16',
        static::INITIALIZER_KEY => function (...$arguments) {
          return new HexEncoder();
        },
      ],
      Base64Encoder::getFormatName() => [
        static::DESCRIPTION_KEY => 'Base64',
        static::INITIALIZER_KEY => function (...$arguments) {
          return new Base64Encoder();
        },
      ],
    ];
  }

}
