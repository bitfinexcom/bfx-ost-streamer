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

namespace Bitfinex\Data\Streamer\osTicket\Actions\UseCases;

/**
 * The osTicket abstract use cases alter signal.
 */
abstract class AbstractUseCasesAlterSignal extends UseCasesAlterSignal {

  /**
   * {@inheritDoc}
   */
  public static function getName() : string {
    return parent::getName() . static::NAME_SEPARATOR . static::getContext();
  }

  /**
   * Get the use case machine name.
   *
   * @return string
   *   The use case machine name.
   */
  abstract protected static function getContext() : string;

}
