<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use h3tech\crud\helpers\CrudWidget;

/**
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 * @var array $context
 */
?>
<div class="model-form">

    <?php
    $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => $enableAjaxValidation,
        'enableClientValidation' => $enableClientValidation,
    ]);
    ?>

    <?php
    CrudWidget::renderFormRules($this, $controllerClass::formRules($model), array_merge($context, ['form' => $form]));
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('h3tech/crud/crud', 'Create') : Yii::t('h3tech/crud/crud', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="modal fade" id="ajax" aria-hidden="true">
        <div class="modal-dialog" style="width:90%;">
            <div class="modal-content"></div>
        </div>
    </div>
</div>
