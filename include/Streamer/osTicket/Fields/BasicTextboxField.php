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

use Bitfinex\Data\Validator\Strings\NotEmptyValidator;

/**
 * The basic textbox field.
 */
class BasicTextboxField extends \TextboxField {

  /**
   * {@inheritDoc}
   */
  public static $widget = __NAMESPACE__ . '\Widgets\BasicTextboxWidget';

  /**
   * Default field length.
   *
   * @var int
   */
  const DEFAULT_LENGTH = 0x10;

  /**
   * {@inheritDoc}
   */
  public function getConfigurationOptions() {
    $configuration = parent::getConfigurationOptions();

    $has_length = \array_key_exists('length', $configuration)
      && $configuration['length'] instanceof \FormField;

    if ($has_length) {
      $configuration['length']->set('default', static::DEFAULT_LENGTH);
    }

    foreach (['validator', 'regex', 'validator-error'] as $name) {
      unset($configuration[$name]);
    }

    return $configuration;
  }

  /**
   * {@inheritDoc}
   */
  public function validateEntry($value) {
    if (isset($value) === FALSE && \count($this->{'_errors'}) > 0) {
      return;
    }

    if ($this->get('required') && NotEmptyValidator::isNotValid($value) && $this->hasData()) {
      $this->{'_errors'}[] = $this->getLabel()
        ? \sprintf(__('%s is a required field'), $this->getLabel())
        : __('This is a required field');

      return;
    }

    if (NotEmptyValidator::isValid($value)) {
      $validators = $this->get('validators');

      if (isset($validators) === TRUE) {
        if (\is_array($validators) === TRUE) {
          foreach ($validators as $validator) {
            if (\is_callable($validator) === TRUE) {
              \call_user_func_array($validator, [$this, $value]);
            }
          }
        }
        elseif (\is_callable($validators) === TRUE) {
          \call_user_func_array($validators, [$this, $value]);
        }
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  public function whatChanged($before, $after) {
    if (NotEmptyValidator::isValid($before)) {
      $format = __('changed from <strong>%1$s</strong> to <strong>%2$s</strong>');
    }
    else {
      $format = __('set to <strong>%2$s</strong>');
    }

    return \sprintf($format, $this->display($before), $this->display($after));
  }

  /**
   * {@inheritDoc}
   */
  public function getSearchMethodWidgets() {
    $widgets = parent::getSearchMethodWidgets();

    foreach ($widgets as &$widget) {
      if (\is_array($widget) === TRUE) {
        $widget[0] = \get_class($this);
      }
    }

    return $widgets;
  }

}
