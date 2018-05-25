<?php

$hint = isset($settings['hint']) ? $settings['hint'] : null;

echo $form->field($model, $field)->dropDownList($settings["items"], $settings)->hint($hint);
