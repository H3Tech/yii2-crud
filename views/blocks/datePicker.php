<?php
use kartik\date\DatePicker;

$hint = isset($settings['hint']) ? $settings['hint'] : null;

echo $form->field($model, $field)->widget(DatePicker::className(), [
    'name' => $field,
    'value' => date('Y-m-d', strtotime('+2 days')),
    'options' => ['placeholder' => Yii::t('h3tech/crud/crud', 'Select date...')],
    'readonly' => true,
    'pluginOptions' => [
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true,
    ]
])->hint($hint);
