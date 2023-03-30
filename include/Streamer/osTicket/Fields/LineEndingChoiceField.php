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

namespace Bitfinex\Data\Streamer\osTicket\Fields;

use Bitfinex\Data\Validator\Strings\NotEmptyValidator;

/**
 * The line ending choice field.
 *
 * @property array $_config
 *   A cached version of the field's configuration.
 */
final class LineEndingChoiceField extends \ChoiceField {

  /**
   * {@inheritDoc}
   */
  public function __construct(array $options = []) {
    $has_default = \array_key_exists('default', $options) === TRUE
      && \is_string($options['default']) === TRUE
      && NotEmptyValidator::isValid($options['default']);

    if ($has_default) {
      if ($this->isEncodedOptionKey($options['default']) === FALSE) {
        $options['default'] = $this->encodeOptionKey($options['default']);
      }
    }

    $options['choices'] = [
      $this->encodeOptionKey("\000") => __('Null Character'),
      $this->encodeOptionKey("\n") => __('Line Feed'),
      $this->encodeOptionKey("\u{0085}") => __('Next Line'),
      $this->encodeOptionKey("\u{2028}") => __('Line Separator'),
      $this->encodeOptionKey("\u{2029}") => __('Paragraph Separator'),
      $this->encodeOptionKey("\r\n") => __('Carriage Return + Line Feed'),
    ];

    parent::__construct($options);
  }

  /**
   * {@inheritDoc}
   */
  public function getConfigurationOptions() {
    $configuration = parent::getConfigurationOptions();
    $has_default = \array_key_exists('default', $configuration)
      && $configuration['default'] instanceof \FormField;

    if ($has_default) {
      $configuration['default'] = new static([
        'label' => $configuration['default']->get('label'),
        'hint' => __('Value selected from the list initially'),
        'required' => $configuration['default']->get('required'),
      ]);
    }

    foreach (['choices', 'multiselect'] as $name) {
      unset($configuration[$name]);
    }

    return $configuration;
  }

  /**
   * {@inheritDoc}
   */
  public function getConfiguration() {
    parent::getConfiguration();

    $this->_config['multiselect'] = FALSE;
    return $this->_config;
  }

  /**
   * {@inheritDoc}
   */
  public function to_config($value) {
    if (\is_array($value) === TRUE) {
      unset($value['multiselect']);
    }

    return $value;
  }

  /**
   * Get the decoded clean option key value.
   *
   * @return null|string
   *   The decoded decoded option key value, NULL otherwise.
   */
  public function getCleanDecoded() {
    $value = $this->getClean();

    if (\is_string($value) && $this->isEncodedOptionKey($value)) {
      $decoded = $this->decodeOptionKey($value);

      if (\is_string($decoded) === TRUE) {
        return $decoded;
      }
    }

    return NULL;
  }

  /**
   * Encode an option key.
   *
   * @param string $value
   *   The key value.
   *
   * @return string
   *   The encoded key value.
   */
  protected function encodeOptionKey(string $value) : string {
    return \base64_encode($value);
  }

  /**
   * Decode an encoded option key.
   *
   * @param string $value
   *   The encoded option key.
   *
   * @return bool|string
   *   The decoded option key value, FALSE on error.
   */
  protected function decodeOptionKey(string $value) {
    return \base64_decode($value, TRUE);
  }

  /**
   * Check whether a value is an encoded string.
   *
   * @param string $value
   *   The string value to check.
   *
   * @return bool
   *   Whether the given value is an encoded string.
   */
  protected function isEncodedOptionKey(string $value) : bool {
    $decoded = $this->decodeOptionKey($value);

    if (\is_string($decoded) === TRUE) {
      return $this->encodeOptionKey($decoded) === $value;
    }

    return $decoded;
  }

}
