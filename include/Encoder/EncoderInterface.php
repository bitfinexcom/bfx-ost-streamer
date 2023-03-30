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

namespace Bitfinex\Data\Encoder;

/**
 * The encoder interface.
 */
interface EncoderInterface {

  /**
   * The encoder format group name.
   *
   * @var string
   */
  const FORMAT_GROUP = 'enc';

  /**
   * Encode a payload.
   *
   * @param string $payload
   *   The payload to encode.
   *
   * @return bool|string
   *   The encoded representation of the given payload, FALSE otherwise.
   */
  public function encode(string $payload);

  /**
   * Get the format name of the encoder.
   *
   * @return string
   *   The format name of the encoder.
   */
  public static function getFormatName() : string;

}
