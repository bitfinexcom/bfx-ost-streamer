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

namespace Bitfinex\Data\Record;

use Bitfinex\Data\Tuple\TupleInterface;
use Bitfinex\Data\Serializer\SerializerInterface;

/**
 * The record interface.
 */
interface RecordInterface extends \Serializable {

  /**
   * The record format group name.
   *
   * @var string
   */
  const FORMAT_GROUP = 'rec';

  /**
   * Get the record serializer.
   *
   * @return SerializerInterface
   *   The current record serializer.
   */
  public function getSerializer() : SerializerInterface;

  /**
   * Set the record serializer.
   *
   * @param SerializerInterface $serializer
   *   The serializer to use.
   *
   * @return void
   */
  public function setSerializer(SerializerInterface $serializer);

  /**
   * Update the record collection.
   *
   * @param TupleInterface $collection
   *   The record collection.
   *
   * @return RecordInterface
   *   The instance of the current object.
   */
  public function update(TupleInterface $collection) : RecordInterface;

  /**
   * Get the format name of the record.
   *
   * @return string
   *   The format name of the record.
   */
  public static function getFormatName() : string;

}
