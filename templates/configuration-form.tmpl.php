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

/**
 * Custom form template.
 *
 * @var \Form $form
 *   The configuration form.
 * @var array $options
 *   The collection of options.
 */
?>
<?php if ($form->getTitle()) : ?>
<h1>
  <?php echo \Format::htmlchars($form->getTitle()); ?>:
  <small><?php echo \Format::htmlchars($form->getInstructions()); ?></small>
</h1>
<?php endif; ?>

<?php foreach ($form->getFields() as $field) : ?>
<div id="field<?php echo $field->getWidget()->{'id'}; ?>" class="form-field"<?php echo !$field->isVisible() ? ' style="display:none;"' : ''; ?>>

  <?php if (!$field->isBlockLevel()) : ?>
  <div class="form-field-label<?php echo $field->isRequired() ? ' required' : '' ?>">
    <?php echo \Format::htmlchars($field->getLocal('label')); ?>:

    <?php if ($field->isRequired()) : ?>
    <span class="error">*</span>
    <?php endif; ?>

    <?php if ($field->get('hint')) : ?>
    <div class="faded hint">
      <?php echo \Format::viewableImages($field->getLocal('hint')); ?>
    </div>
    <?php endif; ?>
  </div>

  <div class="form-field-value">
  <?php endif; ?>

    <?php $field->render($options); ?>

    <?php foreach ($field->errors() as $error) : ?>
    <div class="error"><?php echo \Format::htmlchars($error); ?></div>
    <?php endforeach; ?>

  <?php if (!$field->isBlockLevel()) : ?>
  </div>
  <?php endif; ?>
</div>
<?php endforeach; ?>
<style type="text/css">
  .custom-form-field {
    width: 338px;
  }

  .checkbox.custom-form-field {
    width: calc(350px - 1.3em);
  }

  select.custom-form-field,
  .form-field-value .redactor-box {
    width: 350px;
  }

  .form-field div {
    vertical-align: top;
  }

  .form-field div + div {
    padding-left: 10px;
  }

  .form-field .hint {
    font-size: 95%;
  }

  .form-field {
    margin-top: 5px;
    padding: 5px 0;
  }

  .form-field-label {
    display: inline-block;
    width: 27%;
  }

  .form-field-value {
    display: inline-block;
    max-width: 73%
  }
</style>
