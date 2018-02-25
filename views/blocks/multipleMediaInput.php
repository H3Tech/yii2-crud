<?php

use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;
use yii\helpers\Url;
use h3tech\crud\Module;

$options = isset($settings['options']) ? $settings['options'] : [];
$pluginOptions = isset($settings['pluginOptions']) ? $settings['pluginOptions'] : [];

if ($model->isNewRecord) {
    echo $form->field($model, $field)->widget(FileInput::className(), [
        'options' => array_merge($options, ['multiple' => true]),
        'pluginOptions' => array_merge([
            'showClose' => false,
            'showRemove' => true,
            'showUpload' => false,
        ], $pluginOptions)
    ]);
} else {
    $modelIdAttribute = $settings['modelIdAttribute'];
    $mediaIdAttribute = $settings['mediaIdAttribute'];

    $preview = MediaController::getMultiplePreviewData(
        $model->primaryKey,
        $settings['junctionModelClass'],
        $modelIdAttribute,
        $mediaIdAttribute
    );

    echo $form->field($model, $field)->widget(FileInput::className(), [
        'options' => array_merge($options, ['multiple' => true]),
        'pluginOptions' => array_merge([
            'showClose' => false,
            'uploadUrl' => Url::to(['/media/upload']),
            'uploadAsync' => true,
            'overwriteInitial' => false,
            'initialPreviewAsData' => true,
            'initialPreviewFileType' => 'other',
            'initialPreview' => $preview['initialPreview'],
            'initialPreviewConfig' => $preview['initialPreviewConfig'],
            'uploadExtraData' => [
                'type' => $settings['type'],
                'prefix' => isset($settings['prefix']) ? $settings['prefix'] : null,
                'modelName' => $modelName,
                'modelId' => $model->primaryKey,
                'junctionModelClass' => $settings['junctionModelClass'],
                'mediaIdAttribute' => $mediaIdAttribute,
                'modelIdAttribute' => $modelIdAttribute,
            ],
        ], $pluginOptions),
    ]);
}
