<?php
$settings = array_merge(["maxLength" => true], $settings);
echo $form->field($model, $field)->textarea($settings)->hint(null);
