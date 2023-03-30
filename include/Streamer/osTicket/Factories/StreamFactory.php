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

use Bitfinex\Data\Stream\StreamInterface;
use Bitfinex\Data\Stream\BeanstalkStream;
use Bitfinex\Data\Streamer\osTicket\Configuration\Helper;
use Bitfinex\Data\Stream\StreamFactory as UnbindedStreamFactory;
use Bitfinex\Data\Streamer\osTicket\Actions\Formats\StreamFormatsAlterSignal;

/**
 * The osTicket stream factory.
 */
class StreamFactory extends UnbindedStreamFactory {

  /**
   * Construct the osTicket stream factory.
   *
   * @return self
   */
  public function __construct() {
    parent::__construct(new StreamFormatsAlterSignal());
  }

  /**
   * {@inheritDoc}
   */
  protected function options() : array {
    $options = parent::options();

    $options[BeanstalkStream::getFormatName()][static::INITIALIZER_KEY] = [
      $this,
      'createBeanstalkStream',
    ];

    return $options;
  }

  /**
   * Create a Beanstalk stream.
   *
   * @param mixed ...$arguments
   *   A list of arguments suitable for the Beanstalk stream constructor. If
   *   an argument is not provided, the Osticket configuration will be used.
   *
   * @return StreamInterface
   *   A new instance of the Beanstalk stream.
   */
  protected function createBeanstalkStream(...$arguments) : StreamInterface {
    if (\array_key_exists(0, $arguments) === FALSE) {
      $arguments[0] = Helper::getBeanstalkStreamHostName();
    }

    if (\array_key_exists(1, $arguments) === FALSE) {
      $arguments[1] = Helper::getBeanstalkStreamPortNumber();
    }

    if (\array_key_exists(2, $arguments) === FALSE) {
      $arguments[2] = Helper::getBeanstalkStreamPriority();
    }

    if (\array_key_exists(3, $arguments) === FALSE) {
      $arguments[3] = Helper::getBeanstalkStreamDelay();
    }

    if (\array_key_exists(4, $arguments) === FALSE) {
      $arguments[4] = Helper::getBeanstalkStreamTimeToRun();
    }

    return new BeanstalkStream(...$arguments);
  }

}
