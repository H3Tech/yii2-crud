<?php

namespace h3tech\crud\helpers;

use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;

class CrudWidget
{
    public static function multipleMediaDisplayAttribute($attribute, $junctionClass, $mediaIdField, $modelIdField)
    {
        return [
            'attribute' => $attribute,
            'format' => 'raw',
            'value' => function ($model) use ($attribute, $junctionClass, $mediaIdField, $modelIdField) {
                $preview = MediaController::getMultiplePreviewData(
                    $model->primaryKey,
                    $junctionClass,
                    $modelIdField,
                    $mediaIdField
                );

                return FileInput::widget([
                    'model' => $model,
                    'attribute' => $attribute,
                    'pluginOptions' => [
                        'showClose' => false,
                        'initialPreviewAsData' => true,
                        'initialPreview' => $preview['initialPreview'],
                        'initialPreviewConfig' => $preview['initialPreviewConfig'],
                        'fileActionSettings' => [
                            'showDrag' => false,
                            'showRemove' => false,
                            'showZoom' => false,
                        ],
                        'showCaption' => false,
                        'browseClass' => 'hidden',
                    ],
                    'readonly' => true,
                ]);
            }
        ];
    }
}
