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

namespace Bitfinex\Data\Validator\Numbers;

use Bitfinex\Data\Validator\ValidatorInterface;

/**
 * The number validator interface.
 */
interface NumberValidatorInterface extends ValidatorInterface {

  /**
   * Get the interval lower endpoint.
   *
   * @return null|int|float
   *   The interval lower endpoint, NULL if not applicable.
   */
  public static function getLowerEndpoint();

  /**
   * Get the interval upper endpoint.
   *
   * @return null|int|float
   *   The interval upper endpoint, NULL if not applicable.
   */
  public static function getUpperEndpoint();

}
