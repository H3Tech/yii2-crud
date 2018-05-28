<?php

$hint = isset($settings['hint']) ? $settings['hint'] : null;

echo $form->field($model, $field)->dropDownList($settings['items'], [
    'prompt' => isset($settings['allowEmptyValue']) && $settings['allowEmptyValue']
        ? Yii::t('h3tech/crud/crud', 'None')
        : null,
])->hint($hint);
