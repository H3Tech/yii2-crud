<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\base\ViewNotFoundException;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

/** @noinspection PhpUndefinedVariableInspection */
$this->title = $modelName;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="model-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
//    foreach ($relativeViewPaths as $viewPath) {
//        try {
//            echo $this->render($viewPath . '_search', ['model' => $searchModel]);
//            break;
//        } catch (ViewNotFoundException $e) {
//            continue;
//        }
//    }
    ?>

    <p>
        <?= Html::a(Yii::t('h3tech/crud/crud', 'Create {modelName}', [
            'modelName' => $modelName
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?
    $columnsArray = [['class' => 'yii\grid\SerialColumn'], ['class' => 'yii\grid\ActionColumn']];
    /** @noinspection PhpUndefinedVariableInspection */
    /** @var \h3tech\crud\controllers\AbstractCRUDController $controllerClass */
    $attributes = $controllerClass::indexAttributes();
    array_splice($columnsArray, 1, 0, $attributes);
    /** @noinspection PhpUndefinedVariableInspection */
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columnsArray,
    ]); ?>

</div>