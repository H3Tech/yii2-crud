<?php

use h3tech\crud\helpers\CrudWidget;

$settings = array_merge(['maxLength' => true], $settings);
$label = isset($settings['label']) ? $settings['label'] : null;
$labelOptions = isset($settings['labelOptions']) ? $settings['labelOptions'] : [];

$disableAutomaticHints = isset($settings['disableAutomaticHints']) ? $settings['disableAutomaticHints'] : false;
$hint = isset($settings['hint']) ? $settings['hint'] : null;
if (!$disableAutomaticHints && $hint === null && $model->getAttributeHint($field) === '') {
    $hint = CrudWidget::getLengthHint($model, $field);
}

echo $form->field($model, $field)->textInput($settings)->label($label, $labelOptions)->hint($hint);
