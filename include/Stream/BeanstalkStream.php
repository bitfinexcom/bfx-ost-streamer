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

use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use Bitfinex\Data\Record\RecordInterface;
use Bitfinex\Data\Validator\Strings\HostnameValidator;
use Bitfinex\Data\Validator\Strings\BeanstalkTubeValidator;
use Bitfinex\Data\Validator\Numbers\PositiveWordValidator;
use Bitfinex\Data\Validator\Numbers\PositiveDoubleWordValidator;
use Bitfinex\Data\Validator\Numbers\NonNegativeDoubleWordValidator;

/**
 * The beanstalk stream.
 */
class BeanstalkStream implements StreamInterface {

  /**
   * The beanstalk TTR (in seconds).
   *
   * @var int
   */
  protected $ttr;

  /**
   * The beanstalk stream.
   *
   * @var Pheanstalk
   */
  protected $stream;

  /**
   * The beanstalk delay (in seconds).
   *
   * @var int
   */
  protected $delay;

  /**
   * The beanstalk priority.
   *
   * @var int
   */
  protected $priority;

  /**
   * Default beanstalk hostname.
   *
   * @var string
   */
  const DEFAULT_HOST = '127.0.0.1';

  /**
   * Default beanstalk TTR.
   *
   * @var int
   */
  const DEFAULT_TTR = PheanstalkInterface::DEFAULT_TTR;

  /**
   * The beanstalk stream format name.
   *
   * @var string
   */
  const FORMAT_NAME = self::FORMAT_GROUP . '_beanstalk';

  /**
   * Default beanstalk port number.
   *
   * @var int
   */
  const DEFAULT_PORT = PheanstalkInterface::DEFAULT_PORT;

  /**
   * Default beanstalk tube name.
   *
   * @var string
   */
  const DEFAULT_TUBE = PheanstalkInterface::DEFAULT_TUBE;

  /**
   * Default beanstalk delay.
   *
   * @var int
   */
  const DEFAULT_DELAY = PheanstalkInterface::DEFAULT_DELAY;

  /**
   * Default beanstalkd priority.
   *
   * @var int
   */
  const DEFAULT_PRIORITY = PheanstalkInterface::DEFAULT_PRIORITY;

  /**
   * Construct a beanstalk stream.
   *
   * @param string $host
   *   The hostname of the beanstalk instance.
   * @param int $port
   *   The port on which the beanstalk instance is listening to.
   * @param int $priority
   *   The job priority, smaller values most favorable.
   * @param int $delay
   *   The number of seconds to wait before starting a job.
   * @param int $ttr
   *   The number of seconds to allow a worker to run a job.
   *
   * @return self
   */
  public function __construct(
    string $host = self::DEFAULT_HOST,
    int $port = self::DEFAULT_PORT,
    int $priority = self::DEFAULT_PRIORITY,
    int $delay = self::DEFAULT_DELAY,
    int $ttr = self::DEFAULT_TTR
  ) {
    $host = HostnameValidator::isValid($host) ? $host : static::DEFAULT_HOST;
    $port = PositiveWordValidator::isValid($port) ? \intval($port) : static::DEFAULT_PORT;

    $this->{'stream'} = new Pheanstalk($host, $port);
    $this->{'ttr'} = PositiveDoubleWordValidator::isValid($ttr) ? \intval($ttr) : static::DEFAULT_TTR;
    $this->{'delay'} = NonNegativeDoubleWordValidator::isValid($delay) ? \intval($delay) : static::DEFAULT_DELAY;
    $this->{'priority'} = NonNegativeDoubleWordValidator::isValid($priority) ? \intval($priority) : static::DEFAULT_PRIORITY;
  }

  /**
   * {@inheritDoc}
   */
  public function append(RecordInterface $record, string $queue = NULL) : bool {
    try {
      if ($queue === NULL) {
        $queue = static::DEFAULT_TUBE;
      }
      elseif (BeanstalkTubeValidator::isNotValid($queue)) {
        throw new \InvalidArgumentException();
      }

      return \is_int($this->{'stream'}
        ->useTube($queue)
        ->put(
          $record->serialize(),
          $this->{'priority'},
          $this->{'delay'},
          $this->{'ttr'}
        )
      );
    }
    catch (\Exception $ex) {
      throw new \RuntimeException($ex->getMessage(), $ex->getCode(), $ex);
    }
  }

  /**
   * {@inheritDoc}
   */
  public static function getFormatName() : string {
    return static::FORMAT_NAME;
  }

}
