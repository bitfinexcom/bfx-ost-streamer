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

namespace Bitfinex\Data\Validator\Strings;

/**
 * The abstract regular expression validator.
 */
abstract class AbstractRegularExpressionValidator implements StringValidatorInterface {

  /**
   * {@inheritDoc}
   */
  abstract public static function getMaximumLength();

  /**
   * {@inheritDoc}
   */
  public static function isValid($value) : bool {
    return static::isNotValid($value) === FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public static function isNotValid($value) : bool {
    $options = [
      'options' => [
        'regexp' => static::getSearchPattern(),
      ],
    ];

    return \filter_var($value, \FILTER_VALIDATE_REGEXP, $options) === FALSE;
  }

  /**
   * Get the regular expression search pattern.
   *
   * @return string
   *   The regular expression search pattern.
   */
  abstract protected static function getSearchPattern() : string;

}
