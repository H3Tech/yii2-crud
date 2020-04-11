<?php

$label = isset($settings['label']) ? $settings['label'] : null;
$labelOptions = isset($settings['labelOptions']) ? $settings['labelOptions'] : [];

$hint = isset($settings['hint']) ? $settings['hint'] : null;
$items = isset($settings['items']) ? $settings['items'] : [];
$allowEmptyValue = isset($settings['allowEmptyValue']) ? $settings['allowEmptyValue'] === true : false;
$options = isset($settings['options']) && is_array($settings['options']) ? $settings['options'] : [];

echo $form->field($model, $field)->dropDownList($items, array_merge([
    'prompt' => $allowEmptyValue ? Yii::t('h3tech/crud/crud', 'None') : null,
], $options))->hint($hint)->label($label, $labelOptions);
