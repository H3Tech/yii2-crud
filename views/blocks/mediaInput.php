<?php

use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;

/* @var $model \yii\db\ActiveRecord */

$preview = MediaController::getSinglePreviewData($model->$field);

$options = isset($settings['options']) ? $settings['options'] : [];
$pluginOptions = isset($settings['pluginOptions']) ? $settings['pluginOptions'] : [];

echo $form->field($model, $settings['modelVariable'])->widget(FileInput::className(), [
    'options' => $options,
    'pluginOptions' => array_merge([
        'showClose' => false,
        'overwriteInitial' => true,
        'initialPreviewAsData' => true,
        'initialPreviewFileType' => 'other',
        'initialPreview' => $preview['initialPreview'],
        'initialPreviewConfig' => $preview['initialPreviewConfig'],
        'showRemove' => $model->isNewRecord,
        'showUpload' => false,
        'fileActionSettings' => [
            'showDelete' => false,
        ],
    ], $pluginOptions),
]);
