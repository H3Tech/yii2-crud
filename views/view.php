<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $controllerClass \h3tech\crud\controllers\AbstractCRUDController */
$titleAttribute = $controllerClass::titleAttribute();
$this->title = $titleAttribute !== null && isset($model->$titleAttribute) ? $model->$titleAttribute : $model->primaryKey;
$this->params['breadcrumbs'][] = ['label' => $modelName, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="model-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('h3tech/crud/crud', 'Update'), ['update', 'id' => $model->primaryKey], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('h3tech/crud/crud', 'Delete'), ['delete', 'id' => $model->primaryKey], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('h3tech/crud/crud', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php
    $fields = $model->attributes();
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $fields,
    ]);
    ?>

</div>