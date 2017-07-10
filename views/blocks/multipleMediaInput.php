<?php
use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;
use yii\helpers\Url;

if($model->isNewRecord) {
    /** @noinspection PhpUndefinedVariableInspection */
    echo $form->field($model, $field)->widget(FileInput::className(), [
        "options" => ["accept" => $settings["accept"], "multiple" => true],
        "pluginOptions" => [
            "showClose" => false,
            "allowedFileExtensions" => $settings["allowedFileExtensions"],
            "showRemove" => true,
            "showUpload" => false,
        ]]);
} else {
    $modelId = $model->getPrimaryKey();
    $table = $settings["tableName"];
    $mediaField = $settings["mediaField"];
    $modelField = $settings["modelField"];

    $picturePreviews = MediaController::getMultiplePreviewData($modelId, $table, $mediaField, $modelField);

    /** @noinspection PhpUndefinedVariableInspection */
    echo $form->field($model, $field)->widget(FileInput::className(), [
        "options"=>[
            "accept" => $settings["accept"],
            "multiple"=>true
        ],
        "pluginOptions" => [
            "showClose" => false,
            "allowedFileExtensions" => $settings["allowedFileExtensions"],
            "uploadUrl" => Url::to(["/media/upload"]),
            "uploadAsync" => true,
            "overwriteInitial" => false,
            "initialPreview" => $picturePreviews["initialPreview"],
            "initialPreviewConfig" => $picturePreviews["initialPreviewConfig"],
            "uploadExtraData" => [
                "type" => $settings["type"],
                "prefix" => isset($settings["prefix"]) ? $settings["prefix"] : null,
                "modelName" => $modelName,
                "modelId" => $modelId,
                "table" => $table,
                "mediaField" => $mediaField,
                "modelField" => $modelField,
            ],
        ]
    ]);
}