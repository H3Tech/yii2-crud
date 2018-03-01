<?php

use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;

/* @var $model \yii\db\ActiveRecord */

$preview = MediaController::getSinglePreviewData($model->$field, $field, get_class($model));

$options = isset($settings['options']) ? $settings['options'] : [];
$pluginOptions = isset($settings['pluginOptions']) ? $settings['pluginOptions'] : [];
$pluginEvents = isset($settings['pluginEvents']) ? $settings['pluginEvents'] : [];

echo $form->field($model, $settings['modelVariable'])->widget(FileInput::className(), [
    'options' => $options,
    'pluginOptions' => array_merge([
        'showClose' => false,
        'initialPreviewAsData' => true,
        'initialPreviewFileType' => 'other',
        'initialPreview' => $preview['initialPreview'],
        'initialPreviewConfig' => $preview['initialPreviewConfig'],
        'showRemove' => $model->isNewRecord,
        'showUpload' => false,
        'fileActionSettings' => [
            'showDelete' => !$model->isNewRecord,
            'showDrag' => false,
        ],
    ], $pluginOptions),
    'pluginEvents' => $pluginEvents,
    'sortThumbs' => false,
])->label($model->getAttributeLabel($field))->hint(null);
