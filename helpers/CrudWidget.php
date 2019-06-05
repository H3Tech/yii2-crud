<?php

namespace h3tech\crud\helpers;

use h3tech\crud\controllers\MediaController;
use kartik\file\FileInput;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use kartik\daterange\DateRangePicker;
use Yii;
use yii\web\View;

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

    public static function gcd($a, $b)
    {
        if ($a < 0) {
            $a = -$a;
        }
        if ($b < 0) {
            $b = -$b;
        }

        while ($b != 0) {
            $a %= $b;
            if ($a == 0) {
                return $b;
            }
            $b %= $a;
        }

        return $a;
    }

    public static function renderFormRules(View $view, array $rules, array $context)
    {
        foreach ($rules as $key => $rule) {
            if (is_numeric($key)) {
                $target = $rule[0];
                $type = $rule[1];
                $settings = isset($rule[2]) ? $rule[2] : [];
            } else {
                $target = $key;
                if (is_array($rule)) {
                    $type = $rule[0];
                    $settings = isset($rule[1]) ? $rule[1] : [];
                } else {
                    $type = $rule;
                    $settings = [];
                }
            }

            if (is_array($target)) {
                foreach ($target as $attribute) {
                    static::renderRule($view, $attribute, $type, $settings, $context);
                }
            } else {
                static::renderRule($view, $target, $type, $settings, $context);
            }
        }
    }

    protected static function renderRule(View $view, $field, $blockType, array $settings, array $context)
    {
        $viewPaths = ArrayHelper::getValue($context, 'viewPaths', []);

        $blockPath = 'blocks/' . $blockType . '.php';

        foreach ($viewPaths as $viewPath) {
            $blockFile = $viewPath . DIRECTORY_SEPARATOR . $blockPath;
            if (file_exists($blockFile)) {
                echo $view->renderFile(
                    $blockFile,
                    array_merge(
                        $context, $settings, ['settings' => $settings, 'context' => $context, 'field' => $field]
                    )
                );
                break;
            }
        }
    }
}
