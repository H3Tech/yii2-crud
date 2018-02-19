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
    foreach ($viewPaths as $viewPath) {
        $viewFile = $viewPath . DIRECTORY_SEPARATOR . '_form.php';

        if (file_exists($viewFile)) {
            echo $this->renderFile($viewFile, array_merge($renderParams, ['renderParams' => $renderParams]));
            break;
        }
    }
    ?>

</div>