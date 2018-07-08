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

    public function create(ActiveRecord $model)
    {
    }

    public function update(ActiveRecord $model)
    {
    }

    public function delete(ActiveRecord $model)
    {
    }
}
