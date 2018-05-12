<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\base\ViewNotFoundException;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $controllerClass \h3tech\crud\controllers\AbstractCRUDController
 */

$this->title = $modelNameLabel;
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
        <?= Html::a(Yii::t('h3tech/crud/crud', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?
    $columnsArray = [['class' => 'yii\grid\SerialColumn'], ['class' => 'yii\grid\ActionColumn']];

    $attributes = [];

    $attributes = array_map(function ($indexAttribute) use ($searchModel) {
        return is_array($indexAttribute) ? $indexAttribute : [
            'attribute' => $indexAttribute,
            'label' => $searchModel->getAttributeLabel($indexAttribute),
        ];
    }, $controllerClass::indexAttributes());

    array_splice($columnsArray, 1, 0, $attributes);

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columnsArray,
    ]); ?>

</div>