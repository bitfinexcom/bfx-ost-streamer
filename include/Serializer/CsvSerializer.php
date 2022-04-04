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
use Bitfinex\Data\Validator\Strings\NotBlankValidator;
use Bitfinex\Data\Validator\Numbers\NonNegativeIntegerValidator;

/**
 * The CSV collection serializer.
 */
class CsvSerializer implements SerializerInterface {

  /**
   * The maximum size of the CSV in memory (in bytes).
   *
   * @var int
   */
  protected $memory;

  /**
   * The escape character (at most one single-byte character).
   *
   * An empty string disables the proprietary escape mechanism.
   *
   * @var string
   */
  protected $escape;

  /**
   * The field delimiter (one single-byte character only).
   *
   * @var string
   */
  protected $separator;

  /**
   * The field enclosure (one single-byte character only).
   *
   * @var string
   */
  protected $enclosure;

  /**
   * Default escape character.
   *
   * @var string
   */
  const DEFAULT_ESCAPE = '\\';

  /**
   * Default field delimiter.
   *
   * @var string
   */
  const DEFAULT_SEPARATOR = ',';

  /**
   * Default field enclosure.
   *
   * @var string
   */
  const DEFAULT_ENCLOSURE = '"';

  /**
   * Default maximum memory, in bytes.
   *
   * @var int
   */
  const DEFAULT_MEMORY = (2 * 1024 * 1024);

  /**
   * The CSV serializer format name.
   *
   * @var string
   */
  const FORMAT_NAME = self::FORMAT_GROUP . '_csv';

  /**
   * Construct a CSV collection serializer.
   *
   * @param string $separator
   *   The field delimiter (one single-byte character only).
   * @param string $enclosure
   *   The field enclosure (one single-byte character only).
   * @param string $escape
   *   The escape character (at most one single-byte character).
   * @param int $memory
   *   The maximum size of the CSV row in memory.
   *
   * @return self
   */
  public function __construct(
    string $separator = self::DEFAULT_SEPARATOR,
    string $enclosure = self::DEFAULT_ENCLOSURE,
    string $escape = self::DEFAULT_ESCAPE,
    int $memory = self::DEFAULT_MEMORY
  ) {
    $this->{'escape'} = NotBlankValidator::isValid($escape) ? \trim($escape)[0] : '';
    $this->{'separator'} = NotEmptyValidator::isValid($separator) ? $separator[0] : static::DEFAULT_SEPARATOR;
    $this->{'memory'} = NonNegativeIntegerValidator::isValid($memory) ? \intval($memory) : static::DEFAULT_MEMORY;
    $this->{'enclosure'} = NotBlankValidator::isValid($enclosure) ? \trim($enclosure)[0] : static::DEFAULT_ENCLOSURE;
  }

  /**
   * {@inheritDoc}
   */
  public function serialize(array $collection) {
    $csv = FALSE;

    if (($fptr = \fopen(\sprintf('php://temp/maxmemory:%u', $this->{'memory'}), 'w+')) !== FALSE) {
      if (\fputcsv($fptr, $collection, $this->{'separator'}, $this->{'enclosure'}, $this->{'escape'}) !== FALSE) {
        $csv = \stream_get_contents($fptr, -1, 0);
      }

      \fclose($fptr);
    }

    return $csv;
  }

  /**
   * {@inheritDoc}
   */
  public static function getFormatName() : string {
    return static::FORMAT_NAME;
  }

}
