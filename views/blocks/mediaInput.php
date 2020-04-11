<?php

use h3tech\crud\assets\CropAsset;
use h3tech\crud\assets\DownloadAsset;
use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;
use yii\helpers\Url;
use h3tech\crud\helpers\CrudWidget;

/**
 * @var \yii\web\View $this
 * @var $model \yii\db\ActiveRecord
 */

$this->registerAssetBundle(DownloadAsset::class);

$options = isset($settings['options']) ? $settings['options'] : [];
$pluginOptions = isset($settings['pluginOptions']) ? $settings['pluginOptions'] : [];
$pluginEvents = isset($settings['pluginEvents']) ? $settings['pluginEvents'] : [];

$actionButtons = isset($settings['actionButtons']) ? $settings['actionButtons'] : [];
$disableDownload = isset($settings['disableDownload']) ? $settings['disableDownload'] : false;
if (!$disableDownload) {
    $downloadUrl = Url::to(['/h3tech-crud/media/download']);
    $downloadButton = '<button type="button" class="btn btn-sm btn-kv btn-default btn-outline-secondary download" title="' . Yii::t('h3tech/crud/crud', 'Download') . '" data-download-url="' . $downloadUrl . '" {dataKey}><i class="glyphicon glyphicon-download"></i></a>';
    array_unshift($actionButtons, $downloadButton);
}

$hint = isset($settings['hint']) ? $settings['hint'] : null;
$allowDeletion = isset($settings['allowDeletion']) ? $settings['allowDeletion'] : true;

$preview = MediaController::getSinglePreviewData($model->$field, $field, get_class($model), $allowDeletion);

$targetSizes = [];
$targetSizes = array_merge($targetSizes, isset($settings['targetSize']) ? [$settings['targetSize']] : []);
$targetSizes = array_merge($targetSizes, isset($settings['targetSizes']) ? $settings['targetSizes'] : []);

if (count($targetSizes) > 0) {
    $this->registerAssetBundle(CropAsset::class);

    $sizes = [];

    $modalUrl = Url::to(['/h3tech-crud/image/render-cropper']);
    $cropCheckUrl = Url::to(['/h3tech-crud/image/get-crops']);
    $cropSaveUrl = Url::to(['/h3tech-crud/image/save-crop']);

    foreach ($targetSizes as $targetSize) {
        $width = $targetSize['width'];
        $height = $targetSize['height'];

        $gcd = CrudWidget::gcd($width, $height);

        $aspectWidth = $width / $gcd;
        $aspectHeight = $height / $gcd;

        $sizes[] = [
            'width' => $width, 'height' => $height, 'aspectWidth' => $aspectWidth, 'aspectHeight' => $aspectHeight,
        ];
    }

    $cropButton = '<button type="button" class="btn btn-sm btn-kv btn-default btn-outline-secondary crop" title="' . Yii::t('h3tech/crud/crud', 'Crop Image') . '" data-modal-url="' . $modalUrl . '" data-crop-check-url="' . $cropCheckUrl . '" data-crop-save-url="' . $cropSaveUrl . '" data-sizes="' . htmlspecialchars(json_encode($sizes), ENT_QUOTES) . '"' . ' {dataKey}><i class="glyphicon glyphicon-scissors"></i></button>';
    if ($model->$field !== null) {
        array_unshift($actionButtons, $cropButton);
    }

    if ($hint === null && ($autoHintCount = count($sizes)) > 0) {
        $sizeHint = '';

        for ($i = 0; $i < $autoHintCount; $i++) {
            $separator = '';
            if ($i === $autoHintCount - 1) {
                $separator = ($autoHintCount > 1) ? (' ' . Yii::t('h3tech/crud/crud', 'or') . ' ') : '';
            } elseif ($i > 0) {
                $separator = ', ';
            }
            $sizeHint .= $separator;

            $sizeHint .= Yii::t(
                'h3tech/crud/crud', '{width}x{height} (or {aspectWidth}:{aspectHeight})', $sizes[$i]
            );
        }

        $hint = Yii::t('h3tech/crud/crud', 'Size should be {0}', [$sizeHint]);
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
        'otherActionButtons' => join('', $actionButtons),
    ], $pluginOptions),
    'pluginEvents' => $pluginEvents,
    'sortThumbs' => false,
])->label($model->getAttributeLabel($field))->hint($hint);
