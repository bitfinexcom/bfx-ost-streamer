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

namespace Bitfinex\Data\Serializer;

use Bitfinex\Data\Validator\Strings\NotEmptyValidator;

/**
 * The NDJSON collection serializer.
 */
class LineDelimitedJsonSerializer extends JsonSerializer {

  /**
   * The boundary between lines.
   *
   * @var string
   */
  protected $delimiter;

  /**
   * Default boundary between lines.
   *
   * @var string
   */
  const DEFAULT_DELIMITER = \PHP_EOL;

  /**
   * The NDJSON serializer format name.
   *
   * @var string
   */
  const FORMAT_NAME = self::FORMAT_GROUP . '_ndjson';

  /**
   * Construct a line-delimited JSON collection serializer.
   *
   * @param string $delimiter
   *   A sequence of one or more characters for specifying the boundary between
   *   lines.
   * @param int $flags
   *   Bitmask consisting of constants described on the JSON constants page.
   * @param int $depth
   *   The maximum depth. Must be greater than zero.
   *
   * @return self
   */
  public function __construct(
    string $delimiter = self::DEFAULT_DELIMITER,
    int $flags = self::DEFAULT_FLAGS,
    int $depth = self::DEFAULT_DEPTH
  ) {
    parent::__construct($flags, $depth);
    $this->{'delimiter'} = NotEmptyValidator::isValid($delimiter) ? $delimiter : static::DEFAULT_DELIMITER;
  }

  /**
   * {@inheritDoc}
   */
  public function serialize(array $collection) {
    if (($ndjson = parent::serialize($collection)) !== FALSE) {
      $ndjson .= $this->{'delimiter'};
    }

    return $ndjson;
  }

  /**
   * {@inheritDoc}
   */
  public static function getFormatName() : string {
    return static::FORMAT_NAME;
  }

}
