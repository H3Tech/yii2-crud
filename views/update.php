<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $modelName string */
/* @var $model \yii\db\ActiveRecord */

$this->title = Yii::t('h3tech/crud/crud', 'Update {modelName} {id}', [
    'modelName' => $modelName,
    'id' => $model->getPrimaryKey()
]);
$this->params['breadcrumbs'][] = ['label' => $modelName, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getPrimaryKey(), 'url' => ['view', 'id' => $model->getPrimaryKey()]];
$this->params['breadcrumbs'][] = Yii::t('h3tech/crud/crud', 'Update');
?>
<div class="model-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    $formFolder = "/";
    $formName = "_form";
    $formExtension = ".php";
    /** @noinspection PhpUndefinedVariableInspection */
    $formFile = $viewPath.$formFolder.$formName.$formExtension;
    /** @noinspection PhpUndefinedVariableInspection */
    $formPath = file_exists($formFile) ? "" : $relativeDefaultViewPath;

    /** @noinspection PhpUndefinedVariableInspection */
    echo $this->render($formPath.$formName, [
        'model' => $model,
        'viewPath' => $viewPath,
        'defaultViewPath' => $defaultViewPath,
        'controllerClass' => $controllerClass,
        'modelName' => $modelName,
    ]);
    ?>

</div>