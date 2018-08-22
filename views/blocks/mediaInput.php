<?php

use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;
use yii\helpers\Url;
use h3tech\crud\helpers\CrudWidget;

/* @var $model \yii\db\ActiveRecord */

$options = isset($settings['options']) ? $settings['options'] : [];
$pluginOptions = isset($settings['pluginOptions']) ? $settings['pluginOptions'] : [];
$pluginEvents = isset($settings['pluginEvents']) ? $settings['pluginEvents'] : [];

$hint = isset($settings['hint']) ? $settings['hint'] : null;
$allowDeletion = isset($settings['allowDeletion']) ? $settings['allowDeletion'] : true;

$preview = MediaController::getSinglePreviewData($model->$field, $field, get_class($model), $allowDeletion);

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
        'otherActionButtons' => $otherActionButtons,
    ], $pluginOptions),
    'pluginEvents' => $pluginEvents,
    'sortThumbs' => false,
])->label($model->getAttributeLabel($field))->hint($hint);
