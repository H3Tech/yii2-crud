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

foreach ($controllerClass::getAssetBundles('index') as $assetBundle) {
    $assetBundle::register($this);
}
?>
<div class="model-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    if ($controllerClass::$enableSearchForm) {
        $context = array_merge($context, ['model' => $searchModel]);

        foreach ($relativeViewPaths as $viewPath) {
            try {
                echo $this->render($viewPath . '_search', array_merge($context, ['context' => $context]));
                break;
            } catch (ViewNotFoundException $e) {
                continue;
            }
        }
    }
    ?>

    <p>
        <?php if (in_array('create', $allowedActions)) : ?>
            <?= Html::a(Yii::t('h3tech/crud/crud', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
        <?php if ($controllerClass::showFilterResetButton()) : ?>
            <?= Html::a(Yii::t('h3tech/crud/crud', 'Reset Filters'), ['index'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?
    $templateString = '';
    $isFirst = true;
    foreach ($controllerClass::itemActions() as $action) {
        if (in_array($action, $allowedActions)) {
            $templateString .= ($isFirst ? '' : ' ') . '{' . $action . '}';
        }
        $isFirst = false;
    }

    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
    ];

    $numberOfActions = count($allowedActions);
    if ($numberOfActions > 1 || ($numberOfActions === 1 && !in_array('index', $allowedActions))) {
        $columns[] = [
            'class' => 'yii\grid\ActionColumn',
            'buttons' => $controllerClass::itemButtons(),
            'visibleButtons' => $controllerClass::visibleButtons(),
            'template' => $templateString,
        ];
    }

    $attributes = [];

    $attributes = array_map(function ($indexAttribute) use ($searchModel) {
        return is_array($indexAttribute) ? $indexAttribute : [
            'attribute' => $indexAttribute,
            'label' => $searchModel->getAttributeLabel($indexAttribute),
        ];
    }, $controllerClass::indexAttributes());

    array_splice($columns, 1, 0, $attributes);

    $listViewClass = $controllerClass::listViewClass();

    $allSizes = $controllerClass::pageSizes();
    $canChangeSize = count($allSizes) > 1;
    if ($canChangeSize) {
        $pageSizes = [];
        foreach ($controllerClass::pageSizes() as $size) {
            $pageSizes[$size] = $size;
        }
        echo \nterms\pagesize\PageSize::widget([
            'defaultPageSize' => $controllerClass::pageSize(),
            'sizes' => $pageSizes,
            'label' => Yii::t('h3tech/crud/crud', 'items per page'),
        ]);
    }

    echo $listViewClass::widget(array_merge([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector' => $canChangeSize ? 'select[name="per-page"]' : null,
        'columns' => $columns,
    ], $controllerClass::gridConfig())); ?>

</div>
