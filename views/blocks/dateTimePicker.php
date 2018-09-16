<?php

use kartik\datetime\DateTimePicker;

/* @var string $field */

$hint = isset($settings['hint']) ? $settings['hint'] : null;
$options = isset($settings['options']) ? $settings['options'] : [];
$pluginOptions = isset($settings['pluginOptions']) ? $settings['pluginOptions'] : [];


echo $form->field($model, $field)->widget(DateTimePicker::className(), [
    'name' => $field,
    'options' => array_merge([
        'placeholder' => Yii::t('h3tech/crud/crud', 'Select time...'),
    ], $options),
    'readonly' => true,
    'pluginOptions' => array_merge([
        'format' => 'yyyy-mm-dd HH:ii',
        'autoclose' => true,
    ], $pluginOptions),
])->hint($hint);
