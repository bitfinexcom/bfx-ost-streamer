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

namespace Bitfinex\Data\Streamer\osTicket\Configuration;

use Bitfinex\Data\Streamer\osTicket\Fields\BasicTextboxField;
use Bitfinex\Data\Streamer\osTicket\Fields\TertiumNonDaturField;
use Bitfinex\Data\Streamer\osTicket\Fields\JsonFlagsChoiceField;
use Bitfinex\Data\Streamer\osTicket\Fields\LineEndingChoiceField;

/**
 * Trait of all classes that manage configuration options.
 */
trait HasConfigurationManagementTrait {

  /**
   * Get configuration as a ChoiceField.
   *
   * @param string $name
   *   The configuration name.
   *
   * @return \ChoiceField
   *   The configuration as a ChoiceField.
   */
  protected static function getConfigAsChoiceField(string $name) : \ChoiceField {
    return static::getConfigAsFormField(
      new \ChoiceField(),
      $name
    );
  }

  /**
   * Get configuration as a BasicTextboxField.
   *
   * @param string $name
   *   The configuration name.
   *
   * @return BasicTextboxField
   *   The configuration as a BasicTextboxField.
   */
  protected static function getConfigAsBasicTextboxField(string $name) : BasicTextboxField {
    return static::getConfigAsFormField(
      new BasicTextboxField(),
      $name
    );
  }

  /**
   * Get configuration as a JsonFlagsChoiceField.
   *
   * @param string $name
   *   The configuration name.
   *
   * @return JsonFlagsChoiceField
   *   The configuration as a JsonFlagsChoiceField.
   */
  protected static function getConfigAsJsonFlagsChoiceField(string $name) : JsonFlagsChoiceField {
    return static::getConfigAsFormField(
      new JsonFlagsChoiceField(),
      $name
    );
  }

  /**
   * Get configuration as a LineEndingChoiceField.
   *
   * @param string $name
   *   The configuration name.
   *
   * @return LineEndingChoiceField
   *   The configuration as a LineEndingChoiceField.
   */
  protected static function getConfigAsLineEndingChoiceField(string $name) : LineEndingChoiceField {
    return static::getConfigAsFormField(
      new LineEndingChoiceField(),
      $name
    );
  }

  /**
   * Get configuration as a TertiumNonDaturField.
   *
   * @param string $name
   *   The configuration name.
   *
   * @return TertiumNonDaturField
   *   The configuration as a TertiumNonDaturField.
   */
  protected static function getConfigAsTertiumNonDaturField(string $name) : TertiumNonDaturField {
    return static::getConfigAsFormField(
      new TertiumNonDaturField(),
      $name
    );
  }

  /**
   * Get configuration as a FormField.
   *
   * @param \FormField $field
   *   The form field instance.
   * @param string $name
   *   The configuration name.
   *
   * @return \FormField
   *   The configuration as a FormField.
   *
   * @phpstan-template T of \FormField
   * @phpstan-param T $field
   * @phpstan-return T
   */
  protected static function getConfigAsFormField(\FormField $field, string $name) : \FormField {
    try {
      $plugin = \PluginManager::getInstance(
        \sprintf('plugins/%s', \basename(\BitfinexStreamerPlugin::PLUGIN_DIR))
      );

      if ($plugin instanceof \Plugin) {
        if (\method_exists($plugin, 'getActiveInstances') === TRUE) {
          $plugin = $plugin->getActiveInstances()->one();
        }

        $config = $plugin->getConfig();

        if ($config instanceof \PluginConfig) {
          $field->setValue($config->get($name));
        }
      }
    }

    catch (\Throwable $ex) {
    }

    return $field;
  }

}
