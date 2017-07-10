<?php
$settings = array_merge(["maxLength" => true], $settings);
/** @noinspection PhpUndefinedVariableInspection */
echo $form->field($model, $field)->textInput($settings)->hint(null);