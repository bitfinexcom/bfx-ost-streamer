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

use Bitfinex\Data\Tuple\Sequence;
use Bitfinex\Data\ActionInterface;
use Bitfinex\Data\Encoder\Utf8Encoder;
use Bitfinex\Data\Record\KinesisRecord;
use Bitfinex\Data\Stream\BeanstalkStream;
use Bitfinex\Data\Serializer\CsvSerializer;
use Bitfinex\Data\Serializer\JsonSerializer;
use Bitfinex\Data\Serializer\LineDelimitedJsonSerializer;
use Bitfinex\Data\Streamer\osTicket\Factories\StreamFactory;
use Bitfinex\Data\Streamer\osTicket\Factories\RecordFactory;
use Bitfinex\Data\Streamer\osTicket\Factories\UseCaseFactory;
use Bitfinex\Data\Streamer\osTicket\Factories\EncoderFactory;
use Bitfinex\Data\Streamer\osTicket\Fields\BasicTextboxField;
use Bitfinex\Data\Streamer\osTicket\Factories\SerializerFactory;
use Bitfinex\Data\Streamer\osTicket\Fields\TertiumNonDaturField;
use Bitfinex\Data\Streamer\osTicket\Fields\JsonFlagsChoiceField;
use Bitfinex\Data\Streamer\osTicket\Fields\LineEndingChoiceField;
use Bitfinex\Data\Streamer\osTicket\Actions\Options\OptionsAlterSignal;
use Bitfinex\Data\Streamer\osTicket\Actions\Options\TupleOptionsAlterSignal;
use Bitfinex\Data\Streamer\osTicket\Fields\Validators\NotEmptyFieldValidator;
use Bitfinex\Data\Streamer\osTicket\Fields\Validators\NotBlankFieldValidator;
use Bitfinex\Data\Streamer\osTicket\Fields\Validators\HostnameFieldValidator;
use Bitfinex\Data\Streamer\osTicket\Actions\Options\RecordOptionsAlterSignal;
use Bitfinex\Data\Streamer\osTicket\Actions\Options\StreamOptionsAlterSignal;
use Bitfinex\Data\Streamer\osTicket\Actions\Options\EncoderOptionsAlterSignal;
use Bitfinex\Data\Streamer\osTicket\Actions\Options\UseCaseOptionsAlterSignal;
use Bitfinex\Data\Streamer\osTicket\Actions\Options\SerializerOptionsAlterSignal;
use Bitfinex\Data\Streamer\osTicket\Fields\Validators\HelpdeskCodeFieldValidator;
use Bitfinex\Data\Streamer\osTicket\Fields\Validators\PositiveWordFieldValidator;
use Bitfinex\Data\Streamer\osTicket\Fields\Validators\BeanstalkTubeFieldValidator;
use Bitfinex\Data\Streamer\osTicket\Fields\Validators\KinesisStreamFieldValidator;
use Bitfinex\Data\Streamer\osTicket\Fields\Validators\PositiveIntegerFieldValidator;
use Bitfinex\Data\Streamer\osTicket\Fields\Validators\PositiveDoubleWordFieldValidator;
use Bitfinex\Data\Streamer\osTicket\Fields\Validators\NonNegativeIntegerFieldValidator;
use Bitfinex\Data\Streamer\osTicket\Fields\Validators\NonNegativeDoubleWordFieldValidator;

/**
 * Configuration class to manage bfx-ost-streamer plugin.
 */
class BitfinexStreamerPluginConfig extends \PluginConfig implements \PluginCustomConfig {

  /**
   * {@inheritDoc}
   */
  public function getOptions() {
    $weight = 0;
    $increment = 100;

    $options = [
      'general_options_subtitle' => new \SectionBreakField([
        'label' => __('General Options'),
        '#weight' => ($weight += $increment),
      ]),
      Helper::CODE => new BasicTextboxField([
        'label' => __('Helpdesk code'),
        'hint' => __('A descriptive code for the helpdesk'),
        'default' => NULL,
        'required' => FALSE,
        'validators' => [
          [HelpdeskCodeFieldValidator::class, 'process'],
        ],
        'configuration' => [
          'length' => HelpdeskCodeFieldValidator::length(),
          'validator-error' => __('Helpdesk code is not valid'),
          'classes' => 'custom-form-field custom-form-field--basictextbox',
        ],
        '#weight' => ($weight += $increment),
      ]),
      'stream_section' => new \SectionBreakField([
        'label' => __('Stream'),
        '#weight' => ($weight += $increment),
      ]),
    ];

    $options += $this->getStreamOptions($weight, $increment);

    foreach ((new UseCaseFactory())->formats() as $name => $label) {
      $options += [
        \sprintf('%s_subtitle', $name) => new \SectionBreakField([
          'label' => \sprintf(__('%s use case'), $label),
          '#weight' => ($weight += $increment),
        ]),
      ];

      $options += $this->getUseCaseOptions($name, $weight, $increment);
    }

    $this->trigger(new OptionsAlterSignal(), $options, [
      'weight' => $weight,
      'increment' => $increment,
    ]);

    \uasort($options, [$this, 'weightSort']);
    return $options;
  }

  /**
   * {@inheritDoc}
   */
  public function renderConfig() {
    $options = [];
    $form = $this->getForm();
    include \BitfinexStreamerPlugin::PLUGIN_DIR . '/templates/configuration-form.tmpl.php';
  }

  /**
   * {@inheritDoc}
   */
  public function renderCustomConfig() {
    $this->renderConfig();
  }

  /**
   * {@inheritDoc}
   */
  public function saveCustomConfig() {
    $errors = [];
    return $this->commitForm($errors);
  }

  /**
   * Get the stream options.
   *
   * @param int $weight
   *   The initial weight.
   * @param int $increment
   *   The weight increment.
   *
   * @return \FormField[]
   *   The array of the stream options.
   */
  protected function getStreamOptions(int &$weight = 0, int &$increment = 10) : array {
    $options = [
      Helper::STREAM => new \ChoiceField([
        'label' => __('Stream'),
        'hint' => __('The stream manager'),
        'default' => NULL,
        'required' => TRUE,
        'choices' => (new StreamFactory())->formats(),
        'configuration' => [
          'multiselect' => FALSE,
          'classes' => 'custom-form-field custom-form-field--choice',
        ],
        '#weight' => ($weight += $increment),
      ]),
      Helper::STREAM_BEANSTALK_HOST => new BasicTextboxField([
        'label' => __('Beanstalk host'),
        'hint' => __('The beanstalk host name'),
        'default' => BeanstalkStream::DEFAULT_HOST,
        'required' => TRUE,
        'validators' => [
          [HostnameFieldValidator::class, 'process'],
        ],
        'configuration' => [
          'length' => HostnameFieldValidator::length(),
          'validator-error' => __('Beanstalk host name is not valid'),
          'classes' => 'custom-form-field custom-form-field--basictextbox',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([Helper::STREAM => BeanstalkStream::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
      Helper::STREAM_BEANSTALK_PORT => new BasicTextboxField([
        'label' => __('Beanstalk port'),
        'hint' => __('The beanstalk port number'),
        'default' => BeanstalkStream::DEFAULT_PORT,
        'required' => TRUE,
        'validators' => [
          [PositiveWordFieldValidator::class, 'process'],
        ],
        'configuration' => [
          'validator-error' => __('Beanstalk port number is not valid'),
          'classes' => 'custom-form-field custom-form-field--basictextbox',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([Helper::STREAM => BeanstalkStream::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
      Helper::STREAM_BEANSTALK_PRIORITY => new BasicTextboxField([
        'label' => __('Beanstalk priority'),
        'hint' => __('The beanstalk job priority'),
        'default' => BeanstalkStream::DEFAULT_PRIORITY,
        'required' => TRUE,
        'validators' => [
          [NonNegativeDoubleWordFieldValidator::class, 'process'],
        ],
        'configuration' => [
          'validator-error' => __('Beanstalk job priority is not valid'),
          'classes' => 'custom-form-field custom-form-field--basictextbox',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([Helper::STREAM => BeanstalkStream::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
      Helper::STREAM_BEANSTALK_DELAY => new BasicTextboxField([
        'label' => __('Beanstalk delay'),
        'hint' => __('Seconds to wait before start a job'),
        'default' => BeanstalkStream::DEFAULT_DELAY,
        'required' => TRUE,
        'validators' => [
          [NonNegativeDoubleWordFieldValidator::class, 'process'],
        ],
        'configuration' => [
          'validator-error' => __('Beanstalk job delay is not valid'),
          'classes' => 'custom-form-field custom-form-field--basictextbox',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([Helper::STREAM => BeanstalkStream::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
      Helper::STREAM_BEANSTALK_TTR => new BasicTextboxField([
        'label' => __('Beanstalk time to run'),
        'hint' => __('Seconds a job can be reserved for'),
        'default' => BeanstalkStream::DEFAULT_TTR,
        'required' => TRUE,
        'validators' => [
          [PositiveDoubleWordFieldValidator::class, 'process'],
        ],
        'configuration' => [
          'validator-error' => __('Beanstalk job TTR is not valid'),
          'classes' => 'custom-form-field custom-form-field--basictextbox',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([Helper::STREAM => BeanstalkStream::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
    ];

    $this->trigger(new StreamOptionsAlterSignal(), $options, [
      'weight' => $weight,
      'increment' => $increment,
    ]);

    $this->updateWeight($options, $weight);
    return $options;
  }

  /**
   * Get the use case options.
   *
   * @param string $usecase
   *   The use case machine name.
   * @param int $weight
   *   The initial weight.
   * @param int $increment
   *   The weight increment.
   *
   * @return \FormField[]
   *   The array of the use case options.
   */
  protected function getUseCaseOptions(string $usecase, int &$weight = 0, int &$increment = 10) : array {
    $options = [
      \sprintf(Helper::USE_CASE_ENABLED_FORMAT, $usecase) => new TertiumNonDaturField([
        'label' => __('Enable this use case'),
        'default' => NULL,
        'required' => FALSE,
        'configuration' => [
          'classes' => 'custom-form-field custom-form-field--boolean',
        ],
        '#weight' => ($weight += $increment),
      ]),
      \sprintf(Helper::USE_CASE_QUEUE_SUFFIX_FORMAT, \sprintf('%s_%s', $usecase, BeanstalkStream::getFormatName())) => new BasicTextboxField([
        'label' => __('Beanstalk tube'),
        'hint' => __('The beanstalk tube name'),
        'default' => BeanstalkStream::DEFAULT_TUBE,
        'required' => TRUE,
        'validators' => [
          [BeanstalkTubeFieldValidator::class, 'process'],
        ],
        'configuration' => [
          'length' => BeanstalkTubeFieldValidator::length(),
          'validator-error' => __('Beanstalk tube name is not valid'),
          'classes' => 'custom-form-field custom-form-field--basictextbox',
        ],
        'visibility' => new \VisibilityConstraint(
          \Q::all([
            Helper::STREAM => BeanstalkStream::getFormatName(),
            \sprintf(Helper::USE_CASE_ENABLED_FORMAT, $usecase) => 1,
          ]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
    ];

    $options += $this->getTupleOptions($usecase, $weight, $increment);
    $options += $this->getRecordOptions($usecase, $weight, $increment);

    $this->trigger(new UseCaseOptionsAlterSignal(), $options, [
      'weight' => $weight,
      'use_case' => $usecase,
      'increment' => $increment,
    ]);

    $this->updateWeight($options, $weight);
    return $options;
  }

  /**
   * Get the tuple options.
   *
   * @param string $usecase
   *   The usecase machine name.
   * @param int $weight
   *   The initial weight.
   * @param int $increment
   *   The weight increment.
   *
   * @return \FormField[]
   *   The array of the tuple options.
   */
  protected function getTupleOptions(string $usecase, int &$weight = 0, int &$increment = 10) : array {
    $tuple_serializer = \sprintf(Helper::USE_CASE_TUPLE_SEQUENCE_SERIALIZER_FORMAT, $usecase);

    $options = [
      $tuple_serializer => new \ChoiceField([
        'label' => __('Data serializer'),
        'hint' => __('The format of the streamed data'),
        'default' => NULL,
        'required' => TRUE,
        'choices' => (new SerializerFactory($usecase, Sequence::getFormatName()))->formats(),
        'configuration' => [
          'multiselect' => FALSE,
          'classes' => 'custom-form-field custom-form-field--choice',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([\sprintf(Helper::USE_CASE_ENABLED_FORMAT, $usecase) => 1]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
    ];

    $options += $this->getSerializerOptions($tuple_serializer, $weight, $increment);

    $this->trigger(new TupleOptionsAlterSignal(), $options, [
      'weight' => $weight,
      'use_case' => $usecase,
      'increment' => $increment,
    ]);

    $this->updateWeight($options, $weight);
    return $options;
  }

  /**
   * Get the record options.
   *
   * @param string $usecase
   *   The use case machine name.
   * @param int $weight
   *   The initial weight.
   * @param int $increment
   *   The weight increment.
   *
   * @return \FormField[]
   *   The array of the record options.
   */
  protected function getRecordOptions(string $usecase, int &$weight = 0, int &$increment = 10) : array {
    $record_type = \sprintf(Helper::USE_CASE_RECORD_FORMAT, $usecase);
    $kinesis_encoder = \sprintf(Helper::USE_CASE_RECORD_KINESIS_ENCODER_FORMAT, $usecase);
    $kinesis_serializer = \sprintf(Helper::USE_CASE_RECORD_KINESIS_SERIALIZER_FORMAT, $usecase);

    $options = [
      $record_type => new \ChoiceField([
        'label' => __('Record type'),
        'hint' => __('The stream record type'),
        'default' => NULL,
        'required' => TRUE,
        'choices' => (new RecordFactory($usecase))->formats(),
        'configuration' => [
          'multiselect' => FALSE,
          'classes' => 'custom-form-field custom-form-field--choice',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([\sprintf(Helper::USE_CASE_ENABLED_FORMAT, $usecase) => 1]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
      \sprintf(Helper::USE_CASE_RECORD_KINESIS_STREAM_FORMAT, $usecase) => new BasicTextboxField([
        'label' => __('Kinesis stream name'),
        'hint' => __('The name of the Kinesis stream'),
        'default' => NULL,
        'required' => TRUE,
        'validators' => [
          [KinesisStreamFieldValidator::class, 'process'],
        ],
        'configuration' => [
          'length' => KinesisStreamFieldValidator::length(),
          'validator-error' => __('Kinesis stream name is not valid'),
          'classes' => 'custom-form-field custom-form-field--basictextbox',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([$record_type => KinesisRecord::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
      $kinesis_encoder => new \ChoiceField([
        'label' => __('Kinesis data encoder'),
        'hint' => __('Encoder used by Kinesis record'),
        'default' => NULL,
        'required' => TRUE,
        'choices' => (new EncoderFactory($usecase, KinesisRecord::getFormatName()))->formats(),
        'configuration' => [
          'multiselect' => FALSE,
          'classes' => 'custom-form-field custom-form-field--choice',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([$record_type => KinesisRecord::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
    ];

    $options += $this->getEncoderOptions($kinesis_encoder, $weight, $increment);

    $options += [
      $kinesis_serializer => new \ChoiceField([
        'label' => __('Kinesis record serializer'),
        'hint' => __('Serializer used by Kinesis record'),
        'default' => NULL,
        'required' => TRUE,
        'choices' => (new SerializerFactory($usecase, KinesisRecord::getFormatName()))->formats(),
        'configuration' => [
          'multiselect' => FALSE,
          'classes' => 'custom-form-field custom-form-field--choice',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([$record_type => KinesisRecord::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
    ];

    $options += $this->getSerializerOptions($kinesis_serializer, $weight, $increment);

    $this->trigger(new RecordOptionsAlterSignal(), $options, [
      'weight' => $weight,
      'use_case' => $usecase,
      'increment' => $increment,
    ]);

    $this->updateWeight($options, $weight);
    return $options;
  }

  /**
   * Get the serializer options.
   *
   * @param string $fid
   *   The id of the parent field.
   * @param int $weight
   *   The initial weight.
   * @param int $increment
   *   The weight increment.
   *
   * @return \FormField[]
   *   The array of the serializer options.
   */
  protected function getSerializerOptions(string $fid, int &$weight = 0, int &$increment = 10) : array {
    $namespace = \substr($fid, 0, \strrpos($fid, '_'));

    $options = [
      \sprintf(Helper::CSV_SEPARATOR_SUFFIX_FORMAT, $namespace) => new BasicTextboxField([
        'label' => __('CSV separator'),
        'hint' => __('The field delimiter'),
        'default' => CsvSerializer::DEFAULT_SEPARATOR,
        'required' => TRUE,
        'validators' => [
          [NotEmptyFieldValidator::class, 'process'],
        ],
        'configuration' => [
          'length' => 1,
          'validator-error' => __('CSV separator is not valid'),
          'classes' => 'custom-form-field custom-form-field--basictextbox',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([$fid => CsvSerializer::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
      \sprintf(Helper::CSV_ENCLOSURE_SUFFIX_FORMAT, $namespace) => new BasicTextboxField([
        'label' => __('CSV enclosure'),
        'hint' => __('The field enclosure'),
        'default' => CsvSerializer::DEFAULT_ENCLOSURE,
        'required' => TRUE,
        'validators' => [
          [NotBlankFieldValidator::class, 'process'],
        ],
        'configuration' => [
          'length' => 1,
          'validator-error' => __('CSV enclosure is not valid'),
          'classes' => 'custom-form-field custom-form-field--basictextbox',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([$fid => CsvSerializer::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
      \sprintf(Helper::CSV_ESCAPE_SUFFIX_FORMAT, $namespace) => new BasicTextboxField([
        'label' => __('CSV escape'),
        'hint' => __('The escape character'),
        'default' => CsvSerializer::DEFAULT_ESCAPE,
        'required' => FALSE,
        'validators' => [
          [NotBlankFieldValidator::class, 'process'],
        ],
        'configuration' => [
          'length' => 1,
          'validator-error' => __('CSV escape is not valid'),
          'classes' => 'custom-form-field custom-form-field--basictextbox',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([$fid => CsvSerializer::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
      \sprintf(Helper::CSV_MEMORY_SUFFIX_FORMAT, $namespace) => new BasicTextboxField([
        'label' => __('CSV maximum memory'),
        'hint' => __('CSV maximum memory, in bytes'),
        'default' => CsvSerializer::DEFAULT_MEMORY,
        'required' => TRUE,
        'validators' => [
          [NonNegativeIntegerFieldValidator::class, 'process'],
        ],
        'configuration' => [
          'validator-error' => __('CSV memory must be non negative'),
          'classes' => 'custom-form-field custom-form-field--basictextbox',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([$fid => CsvSerializer::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
      \sprintf(Helper::JSON_DEPTH_SUFFIX_FORMAT, $namespace) => new BasicTextboxField([
        'label' => __('JSON depth'),
        'hint' => __('The JSON depth'),
        'default' => JsonSerializer::DEFAULT_DEPTH,
        'required' => TRUE,
        'validators' => [
          [PositiveIntegerFieldValidator::class, 'process'],
        ],
        'configuration' => [
          'validator-error' => __('JSON depth must be positive'),
          'classes' => 'custom-form-field custom-form-field--basictextbox',
        ],
        'visibility' => new \VisibilityConstraint(
          \Q::any([
            $fid => JsonSerializer::getFormatName(),
            $fid . '__eq' => LineDelimitedJsonSerializer::getFormatName(),
          ]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
      \sprintf(Helper::JSON_FLAGS_SUFFIX_FORMAT, $namespace) => new JsonFlagsChoiceField([
        'label' => __('JSON flags'),
        'hint' => __('JSON flags to use when serializing'),
        'default' => JsonSerializer::DEFAULT_FLAGS,
        'required' => FALSE,
        'configuration' => [
          'classes' => 'custom-form-field custom-form-field--jsonflagschoice',
        ],
        'visibility' => new \VisibilityConstraint(
            \Q::any([
              $fid => JsonSerializer::getFormatName(),
              $fid . '__eq' => LineDelimitedJsonSerializer::getFormatName(),
            ]),
            \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
      \sprintf(Helper::NDJSON_LINE_ENDING_SUFFIX_FORMAT, $namespace) => new LineEndingChoiceField([
        'label' => __('Line ending'),
        'hint' => __('The NDJSON line ending'),
        'default' => LineDelimitedJsonSerializer::DEFAULT_DELIMITER,
        'required' => TRUE,
        'configuration' => [
          'classes' => 'custom-form-field custom-form-field--lineendingchoice',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([$fid => LineDelimitedJsonSerializer::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
    ];

    $this->trigger(new SerializerOptionsAlterSignal(), $options, [
      'field_id' => $fid,
      'weight' => $weight,
      'increment' => $increment,
      'namespace' => $namespace,
    ]);

    $this->updateWeight($options, $weight);
    return $options;
  }

  /**
   * Get the encoder options.
   *
   * @param string $fid
   *   The id of the parent field.
   * @param int $weight
   *   The initial weight.
   * @param int $increment
   *   The weight increment.
   *
   * @return \FormField[]
   *   The array of the encoder options.
   */
  protected function getEncoderOptions(string $fid, int &$weight = 0, int &$increment = 10) : array {
    $encodings = \mb_list_encodings();
    $namespace = \substr($fid, 0, \strrpos($fid, '_'));

    $options = [
      \sprintf(Helper::UTF8_ENCODING_SUFFIX_FORMAT, $namespace) => new \ChoiceField([
        'label' => __('UTF-8 source encoding'),
        'hint' => __('Encoding used by UTF-8 encoder'),
        'default' => NULL,
        'required' => FALSE,
        'choices' => \array_combine($encodings, $encodings),
        'configuration' => [
          'multiselect' => FALSE,
          'classes' => 'custom-form-field custom-form-field--choice',
        ],
        'visibility' => new \VisibilityConstraint(
          new \Q([$fid => Utf8Encoder::getFormatName()]),
          \VisibilityConstraint::VISIBLE
        ),
        '#weight' => ($weight += $increment),
      ]),
    ];

    $this->trigger(new EncoderOptionsAlterSignal(), $options, [
      'field_id' => $fid,
      'weight' => $weight,
      'increment' => $increment,
      'namespace' => $namespace,
    ]);

    $this->updateWeight($options, $weight);
    return $options;
  }

  /**
   * Sort fields by the weight property.
   *
   * @param \FormField $a
   *   First field for comparison.
   * @param \FormField $b
   *   Second field for comparison.
   *
   * @return int
   *   An integer less than, equal to, or greater than zero if the first field
   *   is considered to be respectively less than, equal to, or greater than
   *   the second.
   */
  protected function weightSort(\FormField $a, \FormField $b) : int {
    $x = \intval($a->get('#weight', 0));
    $y = \intval($b->get('#weight', 0));

    if ($x === $y) {
      return 0;
    }

    return $x < $y ? -1 : 1;
  }

  /**
   * Emit a signal.
   *
   * @param ActionInterface $action
   *   The signal to emit.
   * @param mixed $data
   *   The variable that will be passed to listeners by reference.
   * @param mixed $context
   *   An additional variable that describes the context.
   *
   * @return void
   */
  protected function trigger(ActionInterface $action, &$data, $context = NULL) {
    $action->trigger($data, $context);
  }

  /**
   * Update the weight value if needed.
   *
   * @param array $options
   *   The plugin options collection.
   * @param int $weight
   *   The current weight value.
   *
   * @return void
   */
  protected function updateWeight(array $options, int &$weight) {
    $field = \end($options);

    if ($field instanceof \FormField) {
      $heaviness = \intval($field->get('#weight'));

      if ($heaviness > $weight) {
        $weight = $heaviness;
      }
    }

    \reset($options);
  }

}
