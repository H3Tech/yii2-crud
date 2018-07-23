<?php
use kartik\date\DatePicker;

$label = isset($settings['label']) ? $settings['label'] : null;
$hint = isset($settings['hint']) ? $settings['hint'] : null;

echo $form->field($model, $field)->widget(DatePicker::className(), [
    'name' => $field,
    'options' => ['placeholder' => Yii::t('h3tech/crud/crud', 'Select date...')],
    'readonly' => true,
    'pluginOptions' => [
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true,
        'autoclose' => true,
    ],
])->label($label)->hint($hint);
