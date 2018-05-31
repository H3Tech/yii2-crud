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
$allowedActions = $controllerClass::allowedActions();
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

    <?php if (in_array('create', $allowedActions)) : ?>
        <p>
            <?= Html::a(Yii::t('h3tech/crud/crud', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <?
    $templateString = '';
    $isFirst = true;
    foreach ($controllerClass::itemActions() as $action) {
        if (in_array($action, $allowedActions)) {
            $templateString .= ($isFirst ? '' : ' ') . '{' . $action . '}';
        }
        $isFirst = false;
    }
    $columnsArray = [['class' => 'yii\grid\SerialColumn'], [
        'class' => 'yii\grid\ActionColumn',
        'buttons' => $controllerClass::itemButtons(),
        'template' => $templateString,
    ]];

    $attributes = [];

    $attributes = array_map(function ($indexAttribute) use ($searchModel) {
        return is_array($indexAttribute) ? $indexAttribute : [
            'attribute' => $indexAttribute,
            'label' => $searchModel->getAttributeLabel($indexAttribute),
        ];
    }, $controllerClass::indexAttributes());

    array_splice($columnsArray, 1, 0, $attributes);

    echo GridView::widget(array_merge([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columnsArray,
    ], $controllerClass::gridConfig())); ?>

</div>