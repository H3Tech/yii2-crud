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
    foreach ($viewPaths as $viewPath) {
        $formFile = $viewPath . DIRECTORY_SEPARATOR . '_form.php';

        if (file_exists($formFile)) {
            echo $this->renderFile($formFile, [
                'model' => $model,
                'controllerClass' => $controllerClass,
                'modelName' => $modelName,
                'viewPaths' => $viewPaths,
            ]);

            break;
        }
    }
    ?>

</div>