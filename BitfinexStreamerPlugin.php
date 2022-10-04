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

require_once dirname(__FILE__) . '/lib/autoload.php';

use Bitfinex\Data\Streamer\osTicket\Configuration\Helper;
use Bitfinex\Data\Streamer\osTicket\Factories\UseCaseFactory;
use Bitfinex\Data\Streamer\osTicket\Configuration\BitfinexStreamerPluginConfig;

/**
 * Entry point class to bfx-ost-streamer plugin.
 */
class BitfinexStreamerPlugin extends \Plugin {

  /**
   * {@inheritDoc}
   */
  public $config_class = BitfinexStreamerPluginConfig::class;

  /**
   * The plugin directory path.
   *
   * @var string
   */
  const PLUGIN_DIR = __DIR__;

  /**
   * {@inheritDoc}
   */
  public function bootstrap() {
    $factory = new UseCaseFactory();

    foreach ($factory->formats() as $name => $label) {
      if (Helper::isUseCaseEnabled($name)) {
        $factory->create($name);
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  public function enable() {
    if (($has_requirements = parent::enable()) !== TRUE) {
      return $has_requirements;
    }

    return \db_query(\sprintf("UPDATE %s SET `name` = CONCAT('__', `name`) WHERE `id` = %d AND `name` NOT LIKE '\_\_%%'",
      \DbEngine::getCompiler()->quote(\PLUGIN_TABLE),
      \db_input($this->getId())
    ));
  }

}
