<?php

namespace h3tech\crud\controllers\actions;

use h3tech\crud\controllers\AbstractCRUDController;
use yii\base\BaseObject;
use yii\db\ActiveRecord;

/**
 * @property AbstractCRUDController $controllerClass
 */
abstract class Action extends BaseObject
{
    public $controllerClass;

    public function beforeCreate(ActiveRecord $model)
    {
    }

    public function afterCreate(ActiveRecord $model)
    {
    }

    public function beforeUpdate(ActiveRecord $model)
    {
    }

    public function afterUpdate(ActiveRecord $model)
    {
    }

    public function beforedelete(ActiveRecord $model)
    {
    }

    public function afterDelete(ActiveRecord $model)
    {
    }
}
