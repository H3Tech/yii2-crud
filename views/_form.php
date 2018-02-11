<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="model-form">

    <?php
    $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?php
    $includeFunction = function ($field, $blockType, $settings)
    use ($viewPath, $defaultViewPath, $form, $model) {
        $blockPath = 'blocks/' . $blockType . '.php';

        /** @noinspection PhpUndefinedVariableInspection */
        $blockFile = $viewPath . "/" . $blockPath;
        if (!file_exists($blockFile)) {
            /** @noinspection PhpUndefinedVariableInspection */
            $blockFile = $defaultViewPath . $blockPath;
        }

        /** @noinspection PhpIncludeInspection */
        include $blockFile;
    };

    /** @var \h3tech\crud\controllers\AbstractCRUDController $controllerClass */
    foreach ($controllerClass::viewRules() as $key => $rule) {
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
                $includeFunction($attribute, $type, $settings);
            }
        } else {
            $includeFunction($target, $type, $settings);
        }
    }
    ?>

    <div class="form-group">
        <?= /** @noinspection PhpUndefinedVariableInspection */
        Html::submitButton($model->isNewRecord ? Yii::t('h3tech/crud/crud', 'Create') : Yii::t('h3tech/crud/crud', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>