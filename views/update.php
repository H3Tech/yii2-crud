<?php

use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $modelNameLabel string
 * @var $model \yii\db\ActiveRecord
 * @var $controllerClass \h3tech\crud\controllers\AbstractCRUDController
 * @var array $context
 */

$titleAttribute = $controllerClass::titleAttribute();
$modelTitle = $model->primaryKey;
if ($titleAttribute !== null && isset($model->$titleAttribute)) {
    $modelTitle = $model->$titleAttribute;
    $this->title = Yii::t('h3tech/crud/crud', 'Update {modelTitle}', [
        'modelTitle' => $modelTitle,
    ]);
} else {
    $this->title = Yii::t('h3tech/crud/crud', 'Update {modelName} {id}', [
        'modelName' => $modelNameLabel,
        'id' => $model->primaryKey,
    ]);
}

$this->params['breadcrumbs'][] = $controllerClass::isActionAllowed('index') ? ['label' => $modelNameLabel, 'url' => ['index']] : $modelNameLabel;
$this->params['breadcrumbs'][] = $controllerClass::isActionAllowed('view') ? ['label' => $modelTitle, 'url' => ['view', 'id' => $model->{$controllerClass::idAttribute()}]] : $modelTitle;
$this->params['breadcrumbs'][] = Yii::t('h3tech/crud/crud', 'Update');

foreach ($controllerClass::getAssetBundles('update') as $assetBundle) {
    $assetBundle::register($this);
}
?>
<div class="model-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    foreach ($viewPaths as $viewPath) {
        $formFile = $viewPath . DIRECTORY_SEPARATOR . '_form.php';

        if (file_exists($formFile)) {
            echo $this->renderFile($formFile, array_merge($context, ['context' => $context]));
            break;
        }
    }
    ?>

</div>
