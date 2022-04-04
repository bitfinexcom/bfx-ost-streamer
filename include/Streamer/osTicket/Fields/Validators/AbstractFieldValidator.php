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

namespace Bitfinex\Data\Streamer\osTicket\Fields\Validators;

use Bitfinex\Data\Validator\Strings\NotBlankValidator;
use Bitfinex\Data\Streamer\osTicket\Fields\BasicTextboxField;

/**
 * The abstract form field validator.
 */
abstract class AbstractFieldValidator implements FieldValidatorInterface {

  /**
   * {@inheritDoc}
   */
  public static function length() : int {
    return BasicTextboxField::DEFAULT_LENGTH;
  }

  /**
   * {@inheritDoc}
   */
  public static function process(\FormField $field, $value) {
    if (static::isNotValid($value)) {
      $error = static::error();
      $configuration = $field->getConfiguration();

      $has_message = \array_key_exists('validator-error', $configuration)
        && NotBlankValidator::isValid($configuration['validator-error']);

      if ($has_message) {
        $error = $field->getLocal('validator-error', $configuration['validator-error']);
      }

      $field->addError($error);
    }
  }

  /**
   * Provides default error message.
   *
   * @return string
   *   The default error message string.
   */
  protected static function error() : string {
    return __('Not a valid field value');
  }

  /**
   * Checks whether a value violates at least one constraint of a set.
   *
   * @param mixed $value
   *   The value to be inspected.
   *
   * @return bool
   *   TRUE if value violates a constraint, FALSE otherwise.
   */
  abstract protected static function isNotValid($value) : bool;

}
