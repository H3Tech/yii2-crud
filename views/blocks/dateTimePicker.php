<?php

use kartik\datetime\DateTimePicker;

/* @var string $field */

$hint = isset($settings['hint']) ? $settings['hint'] : null;

echo $form->field($model, $field)->widget(DateTimePicker::className(), [
    'name' => $field,
    'options' => ['placeholder' => Yii::t('h3tech/crud/crud', 'Select time...')],
    'readonly' => true,
    'pluginOptions' => [
        'format' => 'yyyy-mm-dd HH:ii',
        'autoclose' => true,
    ],
])->hint($hint);
