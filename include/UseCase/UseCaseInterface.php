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

namespace Bitfinex\Data\UseCase;

use Bitfinex\Data\Tuple\TupleInterface;
use Bitfinex\Data\Stream\StreamInterface;
use Bitfinex\Data\Record\RecordInterface;

/**
 * The use case interface.
 */
interface UseCaseInterface {

  /**
   * Get the use case stream.
   *
   * @return StreamInterface
   *   The current use case stream.
   */
  public function getStream() : StreamInterface;

  /**
   * Set the use case stream.
   *
   * @param StreamInterface $stream
   *   The stream to use.
   *
   * @return void
   */
  public function setStream(StreamInterface $stream);

  /**
   * Get the use case record.
   *
   * @return RecordInterface
   *   The current use case record.
   */
  public function getRecord() : RecordInterface;

  /**
   * Set the use case record.
   *
   * @param RecordInterface $record
   *   The record to use.
   *
   * @return void
   */
  public function setRecord(RecordInterface $record);

  /**
   * Resolve the use case.
   *
   * @param TupleInterface $tuple
   *   The tuple to which the use case applies.
   * @param string $queue
   *   The queue name.
   *
   * @return void
   */
  public function resolve(TupleInterface $tuple, string $queue = NULL);

  /**
   * Get the format name of the use case.
   *
   * @return string
   *   The format name of the use case.
   */
  public static function getFormatName() : string;

}
