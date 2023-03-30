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

namespace Bitfinex\Data\Serializer;

use Bitfinex\Data\Validator\Numbers\PositiveIntegerValidator;
use Bitfinex\Data\Validator\Numbers\NonNegativeIntegerValidator;

/**
 * The JSON collection serializer.
 */
class JsonSerializer implements SerializerInterface {

  /**
   * Bitmask consisting of constants described on the JSON constants page.
   *
   * @var int
   */
  protected $flags;

  /**
   * The maximum depth. Must be greater than zero.
   *
   * @var int
   */
  protected $depth;

  /**
   * Default maximum depth.
   *
   * @var int
   */
  const DEFAULT_DEPTH = 16;

  /**
   * The JSON serializer format name.
   *
   * @var string
   */
  const FORMAT_NAME = self::FORMAT_GROUP . '_json';

  /**
   * Default bitmask.
   *
   * @var int
   */
  const DEFAULT_FLAGS = \JSON_FORCE_OBJECT | \JSON_NUMERIC_CHECK | \JSON_UNESCAPED_UNICODE | \JSON_PRESERVE_ZERO_FRACTION;

  /**
   * Construct a JSON collection serializer.
   *
   * @param int $flags
   *   Bitmask consisting of constants described on the JSON constants page.
   * @param int $depth
   *   The maximum depth. Must be greater than zero.
   *
   * @return self
   */
  public function __construct(
    int $flags = self::DEFAULT_FLAGS,
    int $depth = self::DEFAULT_DEPTH
  ) {
    $this->{'depth'} = PositiveIntegerValidator::isValid($depth) ? \intval($depth) : static::DEFAULT_DEPTH;
    $this->{'flags'} = NonNegativeIntegerValidator::isValid($flags) ? \intval($flags) : static::DEFAULT_FLAGS;
  }

  /**
   * {@inheritDoc}
   */
  public function serialize(array $collection) {
    return \json_encode($collection, $this->{'flags'}, $this->{'depth'});
  }

  /**
   * {@inheritDoc}
   */
  public static function getFormatName() : string {
    return static::FORMAT_NAME;
  }

}
