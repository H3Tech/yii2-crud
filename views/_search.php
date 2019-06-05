<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use h3tech\crud\helpers\CrudWidget;

/**
 * @var $this yii\web\View
 * @var $form yii\widgets\ActiveForm
 * @var \h3tech\crud\controllers\AbstractCRUDController $controllerClass
 * @var array $context
 */
?>
<div class="model-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php
    if (empty(($searchRules = $controllerClass::searchRules()))) {
        foreach($model->attributes() as $field) {
            echo $form->field($model, $field);
        }
    } else {
        CrudWidget::renderFormRules($this, $searchRules, array_merge($context, ['form' => $form]));
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('h3tech/crud/crud', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('h3tech/crud/crud', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
