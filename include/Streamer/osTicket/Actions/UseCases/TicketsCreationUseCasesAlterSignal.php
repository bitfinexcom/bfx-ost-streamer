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

use Bitfinex\Data\Streamer\osTicket\UseCase\TicketsCreationUseCase;

/**
 * The osTicket tickets_creation use case alter signal.
 */
class TicketsCreationUseCasesAlterSignal extends AbstractUseCasesAlterSignal {

  /**
   * {@inheritDoc}
   */
  protected static function getContext() : string {
    return TicketsCreationUseCase::getFormatName();
  }

}
