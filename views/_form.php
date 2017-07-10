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
    /** @var \h3tech\crud\controllers\AbstractCRUDController $controllerClass */
    foreach($controllerClass::viewRules() as $field => $rule) {
        $ruleArray = is_array($rule) ? $rule : [$rule];
        $blockFolder = "blocks/";
        $blockType = $ruleArray[0];
        $blockExtension = ".php";
        $blockPath = $blockFolder.$blockType.$blockExtension;

        /** @noinspection PhpUndefinedVariableInspection */
        $blockFile = $viewPath."/".$blockPath;
        if (!file_exists($blockFile)) {
            /** @noinspection PhpUndefinedVariableInspection */
            $blockFile = $defaultViewPath.$blockPath;
        }

        $settings = isset($ruleArray[1]) ? $ruleArray[1] : [];
        /** @noinspection PhpIncludeInspection */
        include $blockFile;
    }
    ?>

    <div class="form-group">
        <?= /** @noinspection PhpUndefinedVariableInspection */
        Html::submitButton($model->isNewRecord ? Yii::t('h3tech/crud/crud', 'Create') : Yii::t('h3tech/crud/crud', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>