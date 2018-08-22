<?php

use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;
use yii\helpers\Url;
use h3tech\crud\Module;
use h3tech\crud\helpers\CrudWidget;

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
    ])->hint($hint);
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

    $otherActionButtons = '';
    if (is_array($targetSize = isset($settings['targetSize']) ? $settings['targetSize'] : null)
        && isset($targetSize['width']) && isset($targetSize['height'])) {
        $width = $targetSize['width'];
        $height = $targetSize['height'];

        $gcd = CrudWidget::gcd($width, $height);

        $aspectWidth = $width / $gcd;
        $aspectHeight = $height / $gcd;

        $modalUrl = Url::to(['/h3tech-crud/image/render-cropper']);
        $cropCheckUrl = Url::to(['/h3tech-crud/image/get-crops']);
        $cropSaveUrl = Url::to(['/h3tech-crud/image/save-crop']);

        $otherActionButtons = '<button type="button" class="btn btn-sm btn-kv btn-default btn-outline-secondary crop" title="Edit" data-modal-url="' . $modalUrl . '" data-crop-check-url="' . $cropCheckUrl . '" data-crop-save-url="' . $cropSaveUrl . '" data-aspect-width="' . $width . '" data-aspect-height="' . $height . '" {dataKey}><i class="glyphicon glyphicon-scissors"></i></button>';

        if ($hint === null) {
            $hint = Yii::t(
                'h3tech/crud/crud',
                'Image size should be {width}x{height} (or {aspectWidth}:{aspectHeight})',
                ['width' => $width, 'height' => $height, 'aspectWidth' => $aspectWidth, 'aspectHeight' => $aspectHeight]
            );
        }
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
            'otherActionButtons' => $model->isNewRecord ? '' : $otherActionButtons,
        ], $pluginOptions),
        'pluginEvents' => array_merge($events, $pluginEvents),
        'sortThumbs' => $isOrderable,
    ])->hint($hint);
}
