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

namespace Bitfinex\Data;

/**
 * The abstract factory.
 */
abstract class AbstractFactory {

  /**
   * An action manager.
   *
   * @var null|ActionInterface
   */
  protected $action;

  /**
   * The description key in the mapping array.
   *
   * @var string
   */
  const DESCRIPTION_KEY = 'description';

  /**
   * The initializer key in the mapping array.
   *
   * @var string
   */
  const INITIALIZER_KEY = 'initializer';

  /**
   * Construct a factory.
   *
   * @param ActionInterface $action
   *   The action manager.
   *
   * @return self
   */
  public function __construct(ActionInterface $action = NULL) {
    $this->{'action'} = $action;
  }

  /**
   * Create an entity.
   *
   * @param string $format
   *   The entity format.
   * @param mixed ...$arguments
   *   A list of arguments suitable for the entity constructor.
   *
   * @return mixed
   *   A new instance of the entity. Implementers are encouraged to use
   *   covariance to constrain the return type in concrete classes.
   */
  public function create(string $format, ...$arguments) {
    $mapping = $this->mapping();

    if (\array_key_exists($format, $mapping) === FALSE) {
      throw new \InvalidArgumentException('Not supported format');
    }

    return \call_user_func_array($mapping[$format][static::INITIALIZER_KEY], $arguments);
  }

  /**
   * Get all supported formats.
   *
   * @return string[]
   *   All supported formats.
   */
  public function formats() : array {
    return \array_map(function ($format) {
      return $format[static::DESCRIPTION_KEY] ?: 'unknown';
    }, $this->mapping());
  }

  /**
   * Get all entities mapping.
   *
   * @param mixed $context
   *   A context to pass on to all event listeners.
   *
   * @return array[]
   *   All entities mapping.
   */
  protected function mapping($context = NULL) : array {
    $mapping = $this->options();

    if ($this->{'action'} instanceof ActionInterface) {
      $this->{'action'}->trigger($mapping, $context);
    }

    return $mapping;
  }

  /**
   * Get default options.
   *
   * @return array[]
   *   Default options.
   */
  abstract protected function options() : array;

}
