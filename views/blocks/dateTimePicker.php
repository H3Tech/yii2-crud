<?php

use kartik\datetime\DateTimePicker;

/* @var string $field */

$label = isset($settings['label']) ? $settings['label'] : null;
$hint = isset($settings['hint']) ? $settings['hint'] : null;

echo $form->field($model, $field)->widget(DateTimePicker::className(), [
    'name' => $field,
    'options' => ['placeholder' => Yii::t('h3tech/crud/crud', 'Select time...')],
    'readonly' => true,
    'pluginOptions' => [
        'format' => 'yyyy-mm-dd H:i',
        'autoclose' => true,
    ],
])->label($label)->hint($hint);
