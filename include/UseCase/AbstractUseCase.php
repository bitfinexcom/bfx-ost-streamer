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
 * An abstract use case.
 */
abstract class AbstractUseCase implements UseCaseInterface {

  /**
   * The use case stream.
   *
   * @var StreamInterface
   */
  protected $stream;

  /**
   * The use case record.
   *
   * @var RecordInterface
   */
  protected $record;

  /**
   * {@inheritDoc}
   */
  public function getStream() : StreamInterface {
    return $this->{'stream'};
  }

  /**
   * {@inheritDoc}
   */
  public function setStream(StreamInterface $stream) {
    $this->{'stream'} = $stream;
  }

  /**
   * {@inheritDoc}
   */
  public function getRecord() : RecordInterface {
    return $this->{'record'};
  }

  /**
   * {@inheritDoc}
   */
  public function setRecord(RecordInterface $record) {
    $this->{'record'} = $record;
  }

  /**
   * {@inheritDoc}
   */
  public function resolve(TupleInterface $tuple, string $queue = NULL) {
    $this->getStream()->append(
      $this->getRecord()->update($tuple),
      $queue
    );
  }

  /**
   * {@inheritDoc}
   */
  abstract public static function getFormatName() : string;

}
