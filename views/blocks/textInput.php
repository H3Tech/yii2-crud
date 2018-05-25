<?php
$settings = array_merge(['maxLength' => true], $settings);
$hint = isset($settings['hint']) ? $settings['hint'] : null;
echo $form->field($model, $field)->textInput($settings)->hint($hint);
