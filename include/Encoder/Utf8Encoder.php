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

use Bitfinex\Data\Validator\Strings\NotBlankValidator;

/**
 * The UTF-8 string encoder.
 */
class Utf8Encoder implements EncoderInterface {

  /**
   * The source encoding.
   *
   * If not specified, the internal encoding will be used.
   *
   * @var string
   */
  protected $encoding;

  /**
   * The utf8 encoder format name.
   *
   * @var string
   */
  const FORMAT_NAME = self::FORMAT_GROUP . '_utf8';

  /**
   * Construct a UTF-8 string encoder.
   *
   * @param string $encoding
   *   The source encoding.
   *
   * @return self
   */
  public function __construct(string $encoding = NULL) {
    if (NotBlankValidator::isValid($encoding) && \in_array($encoding, \mb_list_encodings())) {
      $this->{'encoding'} = $encoding;
    }
    else {
      $this->{'encoding'} = \mb_internal_encoding();
    }
  }

  /**
   * {@inheritDoc}
   */
  public function encode(string $payload) {
    return \mb_convert_encoding($payload, 'UTF-8', $this->{'encoding'});
  }

  /**
   * {@inheritDoc}
   */
  public static function getFormatName() : string {
    return static::FORMAT_NAME;
  }

}
