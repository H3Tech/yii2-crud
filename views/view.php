<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $controllerClass \h3tech\crud\controllers\AbstractCRUDController */
$titleAttribute = $controllerClass::titleAttribute();
$this->title = $titleAttribute !== null && isset($model->$titleAttribute) ? $model->$titleAttribute : $model->primaryKey;
$this->params['breadcrumbs'][] = $controllerClass::isActionAllowed('index') ? ['label' => $modelNameLabel, 'url' => ['index']] : $modelNameLabel;
$this->params['breadcrumbs'][] = $this->title;

foreach ($controllerClass::getAssetBundles('view') as $assetBundle) {
    $assetBundle::register($this);
}
?>
<div class="model-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php foreach ($controllerClass::detailButtons($model) as $action => $button) : ?>
            <?= $button ?>
        <?php endforeach; ?>
    </p>

    <?php
    $fields = $controllerClass::viewAttributes($model);
    $formatterClass = $controllerClass::detailFormatterClass();
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $fields,
        'formatter' => new $formatterClass(),
    ]);
    ?>

</div>
