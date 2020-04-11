<?php

$label = isset($settings['label']) ? $settings['label'] : null;
$labelOptions = isset($settings['labelOptions']) ? $settings['labelOptions'] : [];

$hint = isset($settings['hint']) ? $settings['hint'] : null;

echo $form->field($model, $field)->checkbox($settings)->hint($hint)->label($label, $labelOptions);
