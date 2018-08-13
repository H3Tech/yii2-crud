<?php

use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;

/* @var $model \yii\db\ActiveRecord */

$options = isset($settings['options']) ? $settings['options'] : [];
$pluginOptions = isset($settings['pluginOptions']) ? $settings['pluginOptions'] : [];
$pluginEvents = isset($settings['pluginEvents']) ? $settings['pluginEvents'] : [];

$hint = isset($settings['hint']) ? $settings['hint'] : null;
$allowDeletion = isset($settings['allowDeletion']) ? $settings['allowDeletion'] : true;

$preview = MediaController::getSinglePreviewData($model->$field, $field, get_class($model), $allowDeletion);

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
            'showRemove' => !$model->isNewRecord && $allowDeletion,
            'showDrag' => false,
        ],
    ], $pluginOptions),
    'pluginEvents' => $pluginEvents,
    'sortThumbs' => false,
])->label($model->getAttributeLabel($field))->hint($hint);
