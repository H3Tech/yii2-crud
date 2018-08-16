<?php

namespace h3tech\crud\helpers;

use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;
use yii\helpers\StringHelper;
use kartik\daterange\DateRangePicker;
use Yii;

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

    public static function multipleMediaDisplayAttribute($attribute, $junctionClass, $mediaIdField, $modelIdField,
                                                         $orderAttribute = null)
    {
        return static::getDisplayAttribute($attribute,
            function ($model) use ($attribute, $junctionClass, $mediaIdField, $modelIdField, $orderAttribute) {
                return static::getMediaDisplayWidget(
                    $model,
                    $attribute,
                    MediaController::getMultiplePreviewData(
                        $model->primaryKey,
                        $junctionClass,
                        $modelIdField,
                        $mediaIdField,
                        $orderAttribute
                    )
                );
            });
    }

    public static function datePickerFilterDefinition($searchModelClass, $attribute)
    {
        $modelName = StringHelper::baseName($searchModelClass);

        return [
            'attribute' => $attribute,
            'filter' => '<div class="drp-container input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>' .
                DateRangePicker::widget([
                    'name' => $modelName . '[' . $attribute . ']',
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'YYYY-MM-DD HH:mm:ss',
                            'separator' => '_',
                        ],
                        'opens' => 'left',
                        'timePicker' => true,
                        'timePicker24Hour' => true,
                    ],
                    'value' => isset(Yii::$app->request->queryParams[$modelName][$attribute])
                        ? Yii::$app->request->queryParams[$modelName][$attribute]
                        : null,
                ]) . '</div>',
        ];
    }
}
