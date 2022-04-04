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

namespace Bitfinex\Data\Streamer\osTicket\Configuration;

use Bitfinex\Data\Tuple\Sequence;
use Bitfinex\Data\Encoder\Utf8Encoder;
use Bitfinex\Data\Record\KinesisRecord;
use Bitfinex\Data\Stream\StreamInterface;
use Bitfinex\Data\Record\RecordInterface;
use Bitfinex\Data\Stream\BeanstalkStream;
use Bitfinex\Data\Encoder\EncoderInterface;
use Bitfinex\Data\Serializer\CsvSerializer;
use Bitfinex\Data\Serializer\JsonSerializer;
use Bitfinex\Data\Serializer\SerializerInterface;
use Bitfinex\Data\Serializer\LineDelimitedJsonSerializer;

/**
 * The configuration helper class.
 */
class Helper {

  use HasConfigurationManagementTrait;

  /**
   * The helpdesk code option.
   *
   * @var string
   */
  const CODE = 'code';

  /**
   * The stream manager option.
   *
   * @var string
   */
  const STREAM = StreamInterface::FORMAT_GROUP;

  /**
   * The Beanstalk host option.
   *
   * @var string
   */
  const STREAM_BEANSTALK_HOST = BeanstalkStream::FORMAT_NAME . '_host';

  /**
   * The Beanstalk port option.
   *
   * @var string
   */
  const STREAM_BEANSTALK_PORT = BeanstalkStream::FORMAT_NAME . '_port';

  /**
   * The Beanstalk priority option.
   *
   * @var string
   */
  const STREAM_BEANSTALK_PRIORITY = BeanstalkStream::FORMAT_NAME . '_priority';

  /**
   * The Beanstalk delay option.
   *
   * @var string
   */
  const STREAM_BEANSTALK_DELAY = BeanstalkStream::FORMAT_NAME . '_delay';

  /**
   * The Beanstalk time to run option.
   *
   * @var string
   */
  const STREAM_BEANSTALK_TTR = BeanstalkStream::FORMAT_NAME . '_ttr';

  /**
   * The format of the use case "enabled" option.
   *
   * @var string
   */
  const USE_CASE_ENABLED_FORMAT = '%s_enabled';

  /**
   * The use case option format for the tuple serializer.
   *
   * @var string
   */
  const USE_CASE_TUPLE_SEQUENCE_SERIALIZER_FORMAT = '%s_' . Sequence::FORMAT_NAME . '_' . SerializerInterface::FORMAT_GROUP;

  /**
   * The format of the use case record option.
   *
   * @var string
   */
  const USE_CASE_RECORD_FORMAT = '%s_' . RecordInterface::FORMAT_GROUP;

  /**
   * The use case option format for the Kinesis record stream.
   *
   * @var string
   */
  const USE_CASE_RECORD_KINESIS_STREAM_FORMAT = '%s_' . KinesisRecord::FORMAT_NAME . '_stream';

  /**
   * The use case option format for the Kinesis record encoder.
   *
   * @var string
   */
  const USE_CASE_RECORD_KINESIS_ENCODER_FORMAT = '%s_' . KinesisRecord::FORMAT_NAME . '_' . EncoderInterface::FORMAT_GROUP;

  /**
   * The use case option format for the Kinesis record serializer.
   *
   * @var string
   */
  const USE_CASE_RECORD_KINESIS_SERIALIZER_FORMAT = '%s_' . KinesisRecord::FORMAT_NAME . '_' . SerializerInterface::FORMAT_GROUP;

  /**
   * The suffix format of the stream queue options.
   *
   * @var string
   */
  const USE_CASE_QUEUE_SUFFIX_FORMAT = '%s_queue';

  /**
   * The suffix format of the UTF-8 encoding options.
   *
   * @var string
   */
  const UTF8_ENCODING_SUFFIX_FORMAT = '%s_' . Utf8Encoder::FORMAT_NAME . '_encoding';

  /**
   * The suffix format of the CSV separator options.
   *
   * @var string
   */
  const CSV_SEPARATOR_SUFFIX_FORMAT = '%s_' . CsvSerializer::FORMAT_NAME . '_separator';

  /**
   * The suffix format of the CSV enclosure options.
   *
   * @var string
   */
  const CSV_ENCLOSURE_SUFFIX_FORMAT = '%s_' . CsvSerializer::FORMAT_NAME . '_enclosure';

  /**
   * The suffix format of the CSV escape options.
   *
   * @var string
   */
  const CSV_ESCAPE_SUFFIX_FORMAT = '%s_' . CsvSerializer::FORMAT_NAME . '_escape';

  /**
   * The suffix format of the CSV maximum memory options.
   *
   * @var string
   */
  const CSV_MEMORY_SUFFIX_FORMAT = '%s_' . CsvSerializer::FORMAT_NAME . '_memory';

  /**
   * The suffix format of the JSON depth options.
   *
   * @var string
   */
  const JSON_DEPTH_SUFFIX_FORMAT = '%s_' . JsonSerializer::FORMAT_NAME . '_depth';

  /**
   * The suffix format of the JSON flags options.
   *
   * @var string
   */
  const JSON_FLAGS_SUFFIX_FORMAT = '%s_' . JsonSerializer::FORMAT_NAME . '_flags';

  /**
   * The suffix format of the NDJSON line ending options.
   *
   * @var string
   */
  const NDJSON_LINE_ENDING_SUFFIX_FORMAT = '%s_' . LineDelimitedJsonSerializer::FORMAT_NAME . '_eol';

  /**
   * Get the helpdesk code.
   *
   * @return null|string
   *   The helpdesk code.
   */
  public static function getHelpdeskCode() {
    return static::getConfigAsBasicTextboxField(
      static::CODE
    )->getClean();
  }

  /**
   * Get the stream format.
   *
   * @return null|string
   *   The stream format.
   */
  public static function getStreamFormat() {
    return static::getConfigAsChoiceField(
      static::STREAM
    )->getClean();
  }

  /**
   * Get the Beanstalk stream host name.
   *
   * @return null|string
   *   The Beanstalk stream host name.
   */
  public static function getBeanstalkStreamHostName() {
    return static::getConfigAsBasicTextboxField(
      static::STREAM_BEANSTALK_HOST
    )->getClean();
  }

  /**
   * Get the Beanstalk stream port number.
   *
   * @return int
   *   The Beanstalk stream port number.
   */
  public static function getBeanstalkStreamPortNumber() : int {
    return \intval(static::getConfigAsBasicTextboxField(
      static::STREAM_BEANSTALK_PORT
    )->getClean());
  }

  /**
   * Get the Beanstalk stream priority.
   *
   * @return int
   *   The Beanstalk stream priority.
   */
  public static function getBeanstalkStreamPriority() : int {
    return \intval(static::getConfigAsBasicTextboxField(
      static::STREAM_BEANSTALK_PRIORITY
    )->getClean());
  }

  /**
   * Get the Beanstalk stream delay.
   *
   * @return int
   *   The Beanstalk stream delay.
   */
  public static function getBeanstalkStreamDelay() : int {
    return \intval(static::getConfigAsBasicTextboxField(
      static::STREAM_BEANSTALK_DELAY
    )->getClean());
  }

  /**
   * Get the Beanstalk stream time to run.
   *
   * @return int
   *   The Beanstalk stream time to run.
   */
  public static function getBeanstalkStreamTimeToRun() : int {
    return \intval(static::getConfigAsBasicTextboxField(
      static::STREAM_BEANSTALK_TTR
    )->getClean());
  }

  /**
   * Check whether the use case is enabled.
   *
   * @param string $context
   *   The use case machine name.
   *
   * @return bool
   *   Whether the use case is enabled.
   */
  public static function isUseCaseEnabled(string $context) : bool {
    return \boolval(static::getConfigAsTertiumNonDaturField(
      \sprintf(static::USE_CASE_ENABLED_FORMAT, $context)
    )->getClean());
  }

  /**
   * Get the tuple format.
   *
   * @param string $context
   *   The use case in which the tuple is used.
   *
   * @return null|string
   *   The tuple format.
   */
  public static function getTupleFormat(string $context) {
    return Sequence::getFormatName();
  }

  /**
   * Get the Sequence tuple serializer format.
   *
   * @param string $context
   *   The use case in which the Sequence tuple is used.
   *
   * @return null|string
   *   The Sequence tuple serializer format.
   */
  public static function getSequenceTupleSerializerFormat(string $context) {
    return static::getConfigAsChoiceField(
      \sprintf(static::USE_CASE_TUPLE_SEQUENCE_SERIALIZER_FORMAT, $context)
    )->getClean();
  }

  /**
   * Get the record format.
   *
   * @param string $context
   *   The use case in which the record is used.
   *
   * @return null|string
   *   The record format.
   */
  public static function getRecordFormat(string $context) {
    return static::getConfigAsChoiceField(
      \sprintf(static::USE_CASE_RECORD_FORMAT, $context)
    )->getClean();
  }

  /**
   * Get the Kinesis record stream name.
   *
   * @param string $context
   *   The use case in which the Kinesis record is used.
   *
   * @return null|string
   *   The Kinesis record stream name.
   */
  public static function getKinesisRecordStreamName(string $context) {
    return static::getConfigAsBasicTextboxField(
      \sprintf(static::USE_CASE_RECORD_KINESIS_STREAM_FORMAT, $context)
    )->getClean();
  }

  /**
   * Get the Kinesis record encoder format.
   *
   * @param string $context
   *   The use case in which the Kinesis record is used.
   *
   * @return null|string
   *   The Kinesis record encoder format.
   */
  public static function getKinesisRecordEncoderFormat(string $context) {
    return static::getConfigAsChoiceField(
      \sprintf(static::USE_CASE_RECORD_KINESIS_ENCODER_FORMAT, $context)
    )->getClean();
  }

  /**
   * Get the Kinesis record serializer format.
   *
   * @param string $context
   *   The use case in which the Kinesis record is used.
   *
   * @return null|string
   *   The Kinesis record serializer format.
   */
  public static function getKinesisRecordSerializerFormat(string $context) {
    return static::getConfigAsChoiceField(
      \sprintf(static::USE_CASE_RECORD_KINESIS_SERIALIZER_FORMAT, $context)
    )->getClean();
  }

  /**
   * Get the stream queue name.
   *
   * @param string $context
   *   The use case in which the queue is used.
   * @param string $format
   *   The format of the stream that uses the queue.
   *
   * @return null|string
   *   The stream queue name.
   */
  public static function getUseCaseQueueName(string $context, string $format) {
    return static::getConfigAsBasicTextboxField(
      \sprintf(static::USE_CASE_QUEUE_SUFFIX_FORMAT, \sprintf('%s_%s', $context, $format))
    )->getClean();
  }

  /**
   * Get the UTF-8 encoder source encoding.
   *
   * @param string $context
   *   The use case in which the encoder is used.
   * @param string $format
   *   The format of the entity that uses the encoder.
   *
   * @return null|string
   *   The UTF-8 encoder source encoding.
   */
  public static function getUtf8EncoderSourceEncondig(string $context, string $format) {
    return static::getConfigAsChoiceField(
      \sprintf(static::UTF8_ENCODING_SUFFIX_FORMAT, \sprintf('%s_%s', $context, $format))
    )->getClean();
  }

  /**
   * Get the CSV serializer field separator.
   *
   * @param string $context
   *   The use case in which the serializer is used.
   * @param string $format
   *   The format of the entity that uses the serializer.
   *
   * @return null|string
   *   The CSV serializer field separator.
   */
  public static function getCsvSerializerFieldSeparator(string $context, string $format) {
    return static::getConfigAsBasicTextboxField(
      \sprintf(static::CSV_SEPARATOR_SUFFIX_FORMAT, \sprintf('%s_%s', $context, $format))
    )->getClean();
  }

  /**
   * Get the CSV serializer field enclosure.
   *
   * @param string $context
   *   The use case in which the serializer is used.
   * @param string $format
   *   The format of the entity that uses the serializer.
   *
   * @return null|string
   *   The CSV serializer field enclosure.
   */
  public static function getCsvSerializerFieldEnclosure(string $context, string $format) {
    return static::getConfigAsBasicTextboxField(
      \sprintf(static::CSV_ENCLOSURE_SUFFIX_FORMAT, \sprintf('%s_%s', $context, $format))
    )->getClean();
  }

  /**
   * Get the CSV serializer escape character.
   *
   * @param string $context
   *   The use case in which the serializer is used.
   * @param string $format
   *   The format of the entity that uses the serializer.
   *
   * @return null|string
   *   The CSV serializer escape character.
   */
  public static function getCsvSerializerEscapeCharacter(string $context, string $format) {
    return static::getConfigAsBasicTextboxField(
      \sprintf(static::CSV_ESCAPE_SUFFIX_FORMAT, \sprintf('%s_%s', $context, $format))
    )->getClean();
  }

  /**
   * Get the CSV serializer memory limit.
   *
   * @param string $context
   *   The use case in which the serializer is used.
   * @param string $format
   *   The format of the entity that uses the serializer.
   *
   * @return int
   *   The CSV serializer memory limit.
   */
  public static function getCsvSerializerMemoryLimit(string $context, string $format) : int {
    return \intval(static::getConfigAsBasicTextboxField(
      \sprintf(static::CSV_MEMORY_SUFFIX_FORMAT, \sprintf('%s_%s', $context, $format))
    )->getClean());
  }

  /**
   * Get the JSON serializer depth.
   *
   * @param string $context
   *   The use case in which the serializer is used.
   * @param string $format
   *   The format of the entity that uses the serializer.
   *
   * @return int
   *   The JSON serializer depth.
   */
  public static function getJsonSerializerDepth(string $context, string $format) : int {
    return \intval(static::getConfigAsBasicTextboxField(
      \sprintf(static::JSON_DEPTH_SUFFIX_FORMAT, \sprintf('%s_%s', $context, $format))
    )->getClean());
  }

  /**
   * Get the JSON serializer flags.
   *
   * @param string $context
   *   The use case in which the serializer is used.
   * @param string $format
   *   The format of the entity that uses the serializer.
   *
   * @return int
   *   The JSON serializer flags.
   */
  public static function getJsonSerializerFlags(string $context, string $format) : int {
    return \intval(static::getConfigAsJsonFlagsChoiceField(
      \sprintf(static::JSON_FLAGS_SUFFIX_FORMAT, \sprintf('%s_%s', $context, $format))
    )->getClean());
  }

  /**
   * Get the NDJSON serializer line ending.
   *
   * @param string $context
   *   The use case in which the serializer is used.
   * @param string $format
   *   The format of the entity that uses the serializer.
   *
   * @return null|string
   *   The NDJSON serializer line ending.
   */
  public static function getLineDelimitedJsonSerializerLineEnding(string $context, string $format) {
    return static::getConfigAsLineEndingChoiceField(
      \sprintf(static::NDJSON_LINE_ENDING_SUFFIX_FORMAT, \sprintf('%s_%s', $context, $format))
    )->getCleanDecoded();
  }

}
