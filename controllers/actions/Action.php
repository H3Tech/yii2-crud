<?php

namespace h3tech\crud\controllers\actions;

use yii\base\BaseObject;
use yii\db\ActiveRecord;

abstract class Action extends BaseObject
{
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
