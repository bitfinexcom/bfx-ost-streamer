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

namespace Bitfinex\Data\Streamer\osTicket\Factories;

use Bitfinex\Data\Tuple\Sequence;
use Bitfinex\Data\Tuple\TupleInterface;
use Bitfinex\Data\Validator\Strings\NotBlankValidator;
use Bitfinex\Data\Streamer\osTicket\Configuration\Helper;
use Bitfinex\Data\Tuple\TupleFactory as UnbindedTupleFactory;
use Bitfinex\Data\Streamer\osTicket\Actions\Formats\TupleFormatsAlterSignal;

/**
 * The osTicket tuple factory.
 */
class TupleFactory extends UnbindedTupleFactory {

  /**
   * The use case in which the tuple is used.
   *
   * @var string
   */
  protected $context;

  /**
   * Construct the osTicket tuple factory.
   *
   * @param string $context
   *   The use case in which the tuple is used.
   *
   * @return self
   */
  public function __construct(string $context) {
    parent::__construct(new TupleFormatsAlterSignal());

    if (NotBlankValidator::isNotValid($context)) {
      throw new \InvalidArgumentException();
    }

    $this->{'context'} = $context;
  }

  /**
   * {@inheritDoc}
   */
  protected function mapping($context = NULL) : array {
    if (isset($context) === FALSE) {
      $context = [
        'context' => $this->{'context'},
      ];
    }

    return parent::mapping($context);
  }

  /**
   * {@inheritDoc}
   */
  protected function options() : array {
    $options = parent::options();

    $options[Sequence::getFormatName()][static::INITIALIZER_KEY] = [
      $this,
      'createSequenceTuple',
    ];

    return $options;
  }

  /**
   * Create a Sequence tuple.
   *
   * @param mixed ...$arguments
   *   A list of arguments suitable for the Sequence tuple constructor. If
   *   an argument is not provided, the Osticket configuration will be used.
   *
   * @return TupleInterface
   *   A new instance of the Sequence tuple.
   */
  protected function createSequenceTuple(...$arguments) : TupleInterface {
    if (\array_key_exists(0, $arguments) === FALSE) {
      $arguments[0] = [];
    }

    if (\array_key_exists(1, $arguments) === FALSE) {
      $format = Helper::getSequenceTupleSerializerFormat($this->{'context'});

      $arguments[1] = isset($format) === FALSE
        ? NULL
        : (new SerializerFactory($this->{'context'}, Sequence::getFormatName()))->create($format);
    }

    return new Sequence(...$arguments);
  }

}
