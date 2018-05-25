<?php

use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;
use yii\helpers\Url;
use h3tech\crud\Module;

$hint = isset($settings['hint']) ? $settings['hint'] : null;

$options = isset($settings['options']) ? $settings['options'] : [];
$pluginOptions = isset($settings['pluginOptions']) ? $settings['pluginOptions'] : [];
$pluginEvents = isset($settings['pluginEvents']) ? $settings['pluginEvents'] : [];
$orderAttribute = isset($settings['orderAttribute']) ? $settings['orderAttribute'] : null;
$isOrderable = $orderAttribute !== null;

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
        $mediaIdAttribute,
        $orderAttribute
    );

    $events = [];

    if ($isOrderable) {
        $orderUrl = Url::to(['/h3tech-crud/media/order']);
        $escapedJunctionModelClass = addslashes($junctionModelClass);

        $events['filesorted'] = <<<JS
function (event, params) {
    var mediaIds = [];
    for (var i = 0; i < params.stack.length; i++) {
        mediaIds.push(params.stack[i].key);
    }
    
    $.ajax('$orderUrl', {
        method: 'POST',
        dataType: 'json',
        data: {
            junctionModelClass: '$escapedJunctionModelClass',
            mediaIdAttribute: '{$settings['mediaIdAttribute']}',
            orderAttribute: '{$settings['orderAttribute']}',
            mediaIds: mediaIds
        }
    });
}
JS;
    }

    echo $form->field($model, $field)->widget(FileInput::className(), [
        'options' => array_merge($options, ['multiple' => true]),
        'pluginOptions' => array_merge([
            'showClose' => false,
            'uploadUrl' => Url::to(['/h3tech-crud/media/upload-multiple']),
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
            'fileActionSettings' => [
                'showDrag' => $isOrderable,
            ],
        ], $pluginOptions),
        'pluginEvents' => array_merge($events, $pluginEvents),
        'sortThumbs' => $isOrderable,
    ])->hint($hint);
}
