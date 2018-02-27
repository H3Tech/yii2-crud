<?php
$settings = array_merge(['maxLength' => true], $settings);
echo $form->field($model, $field)->textInput($settings)->hint(null);
