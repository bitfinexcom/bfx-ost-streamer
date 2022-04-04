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
 * The JSON flags choice field.
 */
final class JsonFlagsChoiceField extends FlagsChoiceField {

  /**
   * {@inheritDoc}
   */
  public function __construct(array $options = []) {
    $options['choices'] = [
      \JSON_HEX_TAG => __('Json Hex Tag'),
      \JSON_HEX_AMP => __('Json Hex Amp'),
      \JSON_HEX_APOS => __('Json Hex Apos'),
      \JSON_HEX_QUOT => __('Json Hex Quot'),
      \JSON_FORCE_OBJECT => __('Json Force Object'),
      \JSON_NUMERIC_CHECK => __('Json Numeric Check'),
      \JSON_PRETTY_PRINT => __('Json Pretty Print'),
      \JSON_UNESCAPED_SLASHES => __('Json Unescaped Slashes'),
      \JSON_UNESCAPED_UNICODE => __('Json Unescaped Unicode'),
      \JSON_PARTIAL_OUTPUT_ON_ERROR => __('Json Partial Output On Error'),
      \JSON_PRESERVE_ZERO_FRACTION => __('Json Preserve Zero Fraction'),
    ];

    if (\defined('\JSON_UNESCAPED_LINE_TERMINATORS')) {
      $options['choices'][\JSON_UNESCAPED_LINE_TERMINATORS] = __('Json Unescaped Line Terminators');
    }

    if (\defined('\JSON_INVALID_UTF8_IGNORE')) {
      $options['choices'][\JSON_INVALID_UTF8_IGNORE] = __('Json Invalid UTF-8 Ignore');
    }

    if (\defined('\JSON_INVALID_UTF8_SUBSTITUTE')) {
      $options['choices'][\JSON_INVALID_UTF8_SUBSTITUTE] = __('Json Invalid UTF-8 Substitute');
    }

    \ksort($options['choices']);
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
        'required' => $configuration['default']->get('required'),
        'hint' => __('Value(s) selected from the list initially'),
      ]);
    }

    unset($configuration['choices']);
    return $configuration;
  }

}
