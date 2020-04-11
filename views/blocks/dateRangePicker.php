<?php

use kartik\daterange\DateRangePicker;

/* @var string $field */

$label = isset($settings['label']) ? $settings['label'] : null;
$labelOptions = isset($settings['labelOptions']) ? $settings['labelOptions'] : [];

$hint = isset($settings['hint']) ? $settings['hint'] : null;
$options = isset($settings['options']) ? $settings['options'] : [];
$pluginOptions = isset($settings['pluginOptions']) ? $settings['pluginOptions'] : [];


echo $form->field($model, $field)->widget(DateRangePicker::className(), [
    'name' => $field,
    'pluginOptions' => array_merge([
        'locale' => [
            'format' => 'YYYY-MM-DD HH:mm:ss',
            'separator' => '_',
        ],
        'timePicker' => true,
        'timePicker24Hour' => true,
    ], $pluginOptions),
])->hint($hint)->label($label, $labelOptions);
