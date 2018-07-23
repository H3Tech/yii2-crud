<?php

$label = isset($settings['label']) ? $settings['label'] : null;
$hint = isset($settings['hint']) ? $settings['hint'] : null;

echo $form->field($model, $field)->checkbox($settings)->label($label)->hint($hint);
