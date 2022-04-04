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

namespace Bitfinex\Data\Streamer\osTicket\Fields;

/**
 * The flags choice field.
 */
class FlagsChoiceField extends \ChoiceField {

  /**
   * {@inheritDoc}
   */
  public static $widget = __NAMESPACE__ . '\Widgets\FlagsBoxChoicesWidget';

  /**
   * Name of the flag whose value is zero.
   *
   * @var int
   */
  const FLAG_NONE = 0x0000;

  /**
   * {@inheritDoc}
   */
  public function getConfigurationOptions() {
    $options_text = [
      'default' => __('(Enter the sum of keys). Value(s) selected from the list initially'),
      'choices' => __('List choices, one per line. Specify key:value names, defining keys in powers of two, ie 1, 2, 4, and so on. This means the individual flags in combined choices do not overlap.'),
    ];

    $configuration = parent::getConfigurationOptions();

    foreach ($options_text as $option => $text) {
      $has_option = \array_key_exists($option, $configuration)
        && $configuration[$option] instanceof \FormField;

      if ($has_option) {
        $configuration[$option]->set('hint', $text);
      }
    }

    foreach (['prompt', 'multiselect'] as $name) {
      unset($configuration[$name]);
    }

    return $configuration;
  }

  /**
   * {@inheritDoc}
   */
  public function getConfiguration() {
    parent::getConfiguration();

    $this->_config['multiple'] = TRUE;
    return $this->_config;
  }

  /**
   * {@inheritDoc}
   */
  public function to_config($value) {
    if (\is_array($value) === TRUE) {
      unset($value['multiple']);
    }

    return $value;
  }

  /**
   * {@inheritDoc}
   */
  public function to_database($value) {
    if (\is_array($value) === TRUE) {
      return \array_sum(\array_keys($value));
    }

    return $value;
  }

  /**
   * {@inheritDoc}
   */
  public function to_php($value) {
    if (\is_numeric($value) === TRUE) {
      $bitmap = \intval($value);
      $value = [];

      foreach ($this->getChoices() as $flag => $label) {
        if (($bitmap & $flag) !== static::FLAG_NONE) {
          $value[$flag] = $label;
        }
      }
    }

    return $value;
  }

  /**
   * {@inheritDoc}
   */
  public function getClean($validate = TRUE) {
    $value = parent::getClean($validate);

    return \is_array($value) === FALSE
      ? static::FLAG_NONE
      : $this->to_database($value);
  }

  /**
   * {@inheritDoc}
   */
  public function whatChanged($before, $after) {
    if (\is_array($before) === TRUE) {
      $before = \array_keys($before);
    }

    if (\is_array($after) === TRUE) {
      $after = \array_keys($after);
    }

    return parent::whatChanged($before, $after);
  }

  /**
   * {@inheritDoc}
   */
  public function toString($value) {
    if (\is_array($value) === TRUE) {
      return \implode(' | ', $value);
    }

    return parent::toString($value);
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchQ($method, $value, $name = FALSE) {
    $name = $name ?: $this->get('name');

    if (\is_array($value) === TRUE) {
      $constraints = [];

      foreach ($value as $flag => $label) {
        $constraints[] = \Q::any([
          "{$name}__hasbit" => $flag,
        ]);
      }

      $criteria = \Q::any($constraints);
    }
    else {
      $criteria = \Q::all([
        new \Q(["{$name}__isnull" => FALSE]),
        new \Q(["{$name}__gt" => static::FLAG_NONE]),
      ]);
    }

    switch ($method) {
      case 'nset':
      case '!includes':
        return \Q::not($criteria);

      case 'set':
      case 'includes':
        return $criteria;

      default:
        return parent::getSearchQ($method, $value, $name);
    }
  }

}
