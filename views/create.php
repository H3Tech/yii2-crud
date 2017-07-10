<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

/** @noinspection PhpUndefinedVariableInspection */
$this->title = Yii::t('h3tech/crud/crud', "Create {modelName}", [
    'modelName' => $modelName
]);
$this->params['breadcrumbs'][] = ['label' => $modelName, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="model-create">

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