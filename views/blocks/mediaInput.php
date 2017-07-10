<?php
use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;

/** @noinspection PhpUndefinedVariableInspection */
$preview = MediaController::getPreviewData($model->$field);

echo $form->field($model, $settings["modelVariable"])->widget(FileInput::className(), [
        'options' => ['accept' => $settings["accept"]],
        'pluginOptions' => [
            'showClose' => false,
            'allowedFileExtensions' => $settings["allowedFileExtensions"],
            'overwriteInitial' => true,
            'initialPreviewAsData' => true,
            'initialPreview' => $preview["initialPreview"],
            'initialPreviewConfig' => $preview["initialPreviewConfig"],
            'showRemove' => $model->isNewRecord ? true : false,
            'showUpload' => false,
        ]
    ]);