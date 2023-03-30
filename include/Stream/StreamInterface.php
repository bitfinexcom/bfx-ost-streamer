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

namespace Bitfinex\Data\Stream;

use Bitfinex\Data\Record\RecordInterface;

/**
 * The stream interface.
 */
interface StreamInterface {

  /**
   * The stream format group name.
   *
   * @var string
   */
  const FORMAT_GROUP = 'stm';

  /**
   * Append a record to the stream.
   *
   * @param RecordInterface $record
   *   The record to append to the stream.
   * @param string $queue
   *   The queue name where to append the record to.
   *
   * @return bool
   *   TRUE if the record has been appended, FALSE otherwise.
   */
  public function append(RecordInterface $record, string $queue = NULL) : bool;

  /**
   * Get the format name of the stream.
   *
   * @return string
   *   The format name of the stream.
   */
  public static function getFormatName() : string;

}
