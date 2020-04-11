<?php
use kartik\date\DatePicker;

$label = isset($settings['label']) ? $settings['label'] : null;
$labelOptions = isset($settings['labelOptions']) ? $settings['labelOptions'] : [];

$hint = isset($settings['hint']) ? $settings['hint'] : null;
$options = isset($settings['options']) ? $settings['options'] : [];
$pluginOptions = isset($settings['pluginOptions']) ? $settings['pluginOptions'] : [];

echo $form->field($model, $field)->widget(DatePicker::className(), [
    'name' => $field,
    'options' => array_merge(['placeholder' => Yii::t('h3tech/crud/crud', 'Select date...'), $options]),
    'readonly' => true,
    'pluginOptions' => array_merge([
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true,
        'autoclose' => true,
    ], $pluginOptions),
])->hint($hint)->label($label, $labelOptions);
