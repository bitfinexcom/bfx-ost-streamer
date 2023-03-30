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

namespace Bitfinex\Data\Streamer\osTicket\Factories;

use Bitfinex\Data\UseCase\UseCaseInterface;
use Bitfinex\Data\Streamer\osTicket\Configuration\Helper;
use Bitfinex\Data\Streamer\osTicket\UseCase\TicketsCreationUseCase;
use Bitfinex\Data\UseCase\UseCaseFactory as UnbindedUseCaseFactory;
use Bitfinex\Data\Streamer\osTicket\Actions\UseCases\UseCasesAlterSignal;

/**
 * The osTicket use case factory.
 */
class UseCaseFactory extends UnbindedUseCaseFactory {

  /**
   * Construct the osTicket use case factory.
   *
   * @return self
   */
  public function __construct() {
    parent::__construct(new UseCasesAlterSignal());
  }

  /**
   * {@inheritDoc}
   */
  protected function options() : array {
    $options = parent::options();

    $options[TicketsCreationUseCase::getFormatName()] = [
      static::DESCRIPTION_KEY => 'Tickets creation',
      static::INITIALIZER_KEY => [$this, 'createTicketsCreationUseCase'],
    ];

    return $options;
  }

  /**
   * Create a Tickets Creation use case.
   *
   * @param mixed ...$arguments
   *   A list of arguments suitable for the Tickets Creation constructor.
   *   If an argument is not provided, the Osticket configuration will be used.
   *
   * @return UseCaseInterface
   *   A new instance of the Tickets Creation use case.
   */
  protected function createTicketsCreationUseCase(...$arguments) : UseCaseInterface {
    if (\array_key_exists(0, $arguments) === FALSE) {
      $format = Helper::getTupleFormat(TicketsCreationUseCase::getFormatName());

      $arguments[0] = isset($format) === FALSE
        ? NULL
        : (new TupleFactory(TicketsCreationUseCase::getFormatName()))->create($format);
    }

    if (\array_key_exists(1, $arguments) === FALSE) {
      $format = Helper::getRecordFormat(TicketsCreationUseCase::getFormatName());

      $arguments[1] = isset($format) === FALSE
        ? NULL
        : (new RecordFactory(TicketsCreationUseCase::getFormatName()))->create($format);
    }

    if (\array_key_exists(2, $arguments) === FALSE) {
      $format = Helper::getStreamFormat();

      $arguments[2] = isset($format) === FALSE
        ? NULL
        : (new StreamFactory())->create($format);
    }

    if (\array_key_exists(3, $arguments) === FALSE) {
      if (($cname = \get_class($arguments[2])) !== FALSE) {
        $arguments[3] = Helper::getUseCaseQueueName(TicketsCreationUseCase::getFormatName(), $cname::getFormatName());
      }
    }

    return new TicketsCreationUseCase(...$arguments);
  }

}
