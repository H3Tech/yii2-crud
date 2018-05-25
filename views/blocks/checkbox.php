<?php

$hint = isset($settings['hint']) ? $settings['hint'] : null;

echo $form->field($model, $field)->checkbox($settings)->hint($hint);
