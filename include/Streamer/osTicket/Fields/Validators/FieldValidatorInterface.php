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

/**
 * The form field validator interface.
 */
interface FieldValidatorInterface {

  /**
   * Returns the maximum allowed length of the field.
   *
   * @return int
   *   The maximum allowed length of the field.
   */
  public static function length() : int;

  /**
   * Checks whether a value respects a set of constraints.
   *
   * @param \FormField $field
   *   The form field that provides the value.
   * @param mixed $value
   *   The value to be inspected.
   *
   * @return void
   */
  public static function process(\FormField $field, $value);

}
