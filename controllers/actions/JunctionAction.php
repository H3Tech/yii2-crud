<?php

namespace h3tech\crud\controllers\actions;

use yii\db\ActiveRecord;

/**
 * @property ActiveRecord $junctionModelClass
 * @property string $modelField
 * @property string $foreignField
 * @property string $foreignKeyVariable
 */
class JunctionAction extends Action
{
    public $junctionModelClass;
    public $modelField;
    public $foreignField;
    public $foreignKeyVariable;
    public $items = [];

    public function afterCreate(ActiveRecord $model)
    {
        $junctionModelClass = $this->junctionModelClass;

        foreach ($model->{$this->foreignKeyVariable} as $foreignKey) {
            $identity = [
                $this->modelField => $model->primaryKey,
                $this->foreignField => empty($this->items) ? $foreignKey : $this->items[$foreignKey],
            ];

            /** @var ActiveRecord $junctionEntry */
            if (($junctionEntry = $junctionModelClass::find()->where($identity)->one()) === null) {
                $junctionEntry = new $junctionModelClass;
                $junctionEntry->load($identity, '');
                $junctionEntry->save();
            }
        }
    }

    public function afterUpdate(ActiveRecord $model)
    {
        $junctionModelClass = $this->junctionModelClass;
        $foreignKeys = $model->{$this->foreignKeyVariable};

        if (empty($foreignKeys)) {
            $junctionModelClass::deleteAll([$this->modelField => $model->primaryKey]);
        } else {
            $junctionModelClass::deleteAll([
                'and',
                [$this->modelField => $model->primaryKey],
                [
                    'not in',
                    $this->foreignField,
                    empty($this->items)
                        ? $foreignKeys
                        : array_map(function ($key) {
                        return $this->items[$key];
                    }, $foreignKeys),
                ],
            ]);
        }

        foreach ($foreignKeys as $foreignKey) {
            $identity = [
                $this->modelField => $model->primaryKey,
                $this->foreignField => empty($this->items) ? $foreignKey : $this->items[$foreignKey],
            ];

            /** @var ActiveRecord $junctionEntry */
            if (($junctionEntry = $junctionModelClass::find()->where($identity)->one()) === null) {
                $junctionEntry = new $junctionModelClass;
                $junctionEntry->load($identity, '');
                $junctionEntry->save();
            }
        }
    }

    public function beforeDelete(ActiveRecord $model)
    {
        $junctionModelClass = $this->junctionModelClass;
        $junctionModelClass::deleteAll([$this->modelField => $model->primaryKey]);
    }
}
