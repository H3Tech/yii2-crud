<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\helpers\StringHelper;

/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use <?= $generator->baseControllerClass ?>;
use <?= $generator->modelClass ?>;
use <?= $generator->searchModelClass ?>;

class <?= $controllerClass ?> extends AbstractCRUDController
{
    protected static $modelClass = <?= StringHelper::basename($generator->modelClass) ?>::class;
    protected static $searchModelClass = <?= StringHelper::basename($generator->searchModelClass) ?>::class;
}
