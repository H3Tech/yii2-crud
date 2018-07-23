<?php
$settings = array_merge(['maxLength' => true], $settings);
$label = isset($settings['label']) ? $settings['label'] : null;
$hint = isset($settings['hint']) ? $settings['hint'] : null;
echo $form->field($model, $field)->textarea($settings)->label($label)->hint($hint);
