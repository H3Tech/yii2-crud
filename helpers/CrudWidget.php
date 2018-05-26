<?php

namespace h3tech\crud\helpers;

use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;

class CrudWidget
{
    protected static function getDisplayAttribute($attribute, $valueFunction)
    {
        return [
            'attribute' => $attribute,
            'format' => 'raw',
            'value' => $valueFunction,
        ];
    }

    protected static function getMediaDisplayWidget($model, $attribute, $preview)
    {
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
                'dropZoneTitle' => '',
            ],
            'readonly' => true,
        ]);
    }

    public static function mediaDisplayAttribute($attribute)
    {
        return static::getDisplayAttribute($attribute, function ($model) use ($attribute) {
            return static::getMediaDisplayWidget(
                $model,
                $attribute,
                MediaController::getSinglePreviewData(
                    $model->$attribute,
                    $attribute,
                    get_class($model)
                )
            );
        });
    }

    public static function multipleMediaDisplayAttribute($attribute, $junctionClass, $mediaIdField, $modelIdField)
    {
        return static::getDisplayAttribute($attribute,
            function ($model) use ($attribute, $junctionClass, $mediaIdField, $modelIdField) {
                return static::getMediaDisplayWidget(
                    $model,
                    $attribute,
                    MediaController::getMultiplePreviewData(
                        $model->primaryKey,
                        $junctionClass,
                        $modelIdField,
                        $mediaIdField
                    )
                );
            });
    }
}
