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

namespace Bitfinex\Data\Streamer\osTicket\UseCase;

use Bitfinex\Data\Tuple\TupleInterface;
use Bitfinex\Data\Stream\StreamInterface;
use Bitfinex\Data\Record\RecordInterface;
use Bitfinex\Data\UseCase\AbstractUseCase;
use Bitfinex\Data\Streamer\osTicket\Configuration\Helper;
use Bitfinex\Data\Streamer\osTicket\Actions\UseCases\TicketsCreationUseCasesAlterSignal;

/**
 * The tickets creation use case.
 */
class TicketsCreationUseCase extends AbstractUseCase {

  /**
   * The data tuple.
   *
   * @var TupleInterface
   */
  protected $tuple;

  /**
   * The stream queue name.
   *
   * @var null|string
   */
  protected $queue;

  /**
   * The action manager.
   *
   * @var \Bitfinex\Data\ActionInterface
   */
  protected $action;

  /**
   * Construct a tickets creation use case.
   *
   * @param TupleInterface $tuple
   *   The use case tuple.
   * @param RecordInterface $record
   *   The use case record.
   * @param StreamInterface $stream
   *   The use case stream.
   * @param string $queue
   *   The stream queue name.
   *
   * @return self
   */
  public function __construct(TupleInterface $tuple, RecordInterface $record, StreamInterface $stream, string $queue = NULL) {
    $this->{'tuple'} = $tuple;
    $this->{'queue'} = $queue;
    $this->{'record'} = $record;
    $this->{'stream'} = $stream;
    $this->{'action'} = new TicketsCreationUseCasesAlterSignal();

    if (\class_exists('\Signal') === TRUE) {
      \call_user_func_array(['\Signal', 'connect'], [
        'ticket.created',
        [$this, 'onTicketCreated'],
      ]);
    }
  }

  /**
   * {@inheritDoc}
   */
  public static function getFormatName() : string {
    return 'tickets_creation';
  }

  /**
   * Callback function for the "ticket.created" event.
   *
   * @param \Ticket $ticket
   *   The newly created ticket.
   *
   * @return void
   */
  public function onTicketCreated(\Ticket $ticket) {
    $data = [
      'helpdesk_code' => Helper::getHelpdeskCode(),
      'datetime_complete' => $ticket->getCreateDate(),
      'department_id' => $ticket->getDeptId(),
      'department_name' => $ticket->getDeptName(),
      'helptopic_id' => $ticket->getTopicId(),
      'helptopic_label' => $ticket->getHelpTopic(),
    ];

    $this->{'action'}->trigger($data, $ticket);

    try {
      $this->resolve(
        $this->{'tuple'}->clear()->addRange($data),
        $this->{'queue'}
      );
    }
    catch (\RuntimeException $ex) {
      $GLOBALS['ost']->logWarning(
        \sprintf('%s (%s)', \basename(\BitfinexStreamerPlugin::PLUGIN_DIR), static::getFormatName()),
        $ex->getMessage(),
        FALSE
      );
    }
  }

}
