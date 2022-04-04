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

namespace Bitfinex\Data\Tuple;

use Bitfinex\Data\Serializer\SerializerInterface;

/**
 * The tuple interface.
 */
interface TupleInterface extends \SeekableIterator, \ArrayAccess, \Serializable, \Countable {

  /**
   * The tuple format group name.
   *
   * @var string
   */
  const FORMAT_GROUP = 'tpl';

  /**
   * Get the tuple serializer.
   *
   * @return SerializerInterface
   *   The current tuple serializer.
   */
  public function getSerializer() : SerializerInterface;

  /**
   * Set the tuple serializer.
   *
   * @param SerializerInterface $serializer
   *   The serializer to use.
   *
   * @return void
   */
  public function setSerializer(SerializerInterface $serializer);

  /**
   * Add an item to the tuple.
   *
   * @param mixed $item
   *   The item to add to the tuple.
   * @param null|int|string $offset
   *   The offset of the item; if not specified, the item is added at the end
   *   of the tuple.
   *
   * @return TupleInterface
   *   The instance of the current object.
   */
  public function add($item, $offset = NULL) : TupleInterface;

  /**
   * Add the items of the specified collection to the tuple, honoring offsets.
   *
   * @param array $collection
   *   The items collection.
   *
   * @return TupleInterface
   *   The instance of the current object.
   */
  public function addRange(array $collection) : TupleInterface;

  /**
   * Remove all elements from the tuple.
   *
   * @return TupleInterface
   *   The instance of the current object.
   */
  public function clear() : TupleInterface;

  /**
   * Get the format name of the tuple.
   *
   * @return string
   *   The format name of the tuple.
   */
  public static function getFormatName() : string;

}
