<?php

use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var array $context
 * @var \h3tech\crud\controllers\AbstractCRUDController $controllerClass
 */

$this->title = Yii::t('h3tech/crud/crud', "Create {modelName}", [
    'modelName' => $modelNameLabel
]);
$this->params['breadcrumbs'][] = $controllerClass::isActionAllowed('index') ? ['label' => $modelNameLabel, 'url' => ['index']] : $modelNameLabel;
$this->params['breadcrumbs'][] = Yii::t('h3tech/crud/crud', 'Create');
?>
<div class="model-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    foreach ($viewPaths as $viewPath) {
        $viewFile = $viewPath . DIRECTORY_SEPARATOR . '_form.php';

        if (file_exists($viewFile)) {
            echo $this->renderFile($viewFile, array_merge($context, ['context' => $context]));
            break;
        }
    }
    ?>

</div>
