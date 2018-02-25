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

    public function create(ActiveRecord $model)
    {
        foreach ($model->{$this->foreignKeyVariable} as $foreignKey) {
            $identity = [
                $this->modelField => $model->primaryKey,
                $this->foreignField => $foreignKey,
            ];

            /** @var ActiveRecord $junctionEntry */
            if (($junctionEntry = ($this->junctionModelClass)::find()->where($identity)->one()) === null) {
                $junctionEntry = new $this->junctionModelClass;
                $junctionEntry->load($identity, '');
                $junctionEntry->save();
            }
        }
    }

    public function update(ActiveRecord $model)
    {
        $foreignKeys = $model->{$this->foreignKeyVariable};

        if (empty($foreignKeys)) {
            ($this->junctionModelClass)::deleteAll([$this->modelField => $model->primaryKey]);
        } else {
            ($this->junctionModelClass)::deleteAll([
                'and',
                [$this->modelField => $model->primaryKey],
                ['not in', $this->foreignField, $foreignKeys],
            ]);
        }

        foreach ($foreignKeys as $foreignKey) {
            $identity = [
                $this->modelField => $model->primaryKey,
                $this->foreignField => $foreignKey,
            ];

            /** @var ActiveRecord $junctionEntry */
            if (($junctionEntry = ($this->junctionModelClass)::find()->where($identity)->one()) === null) {
                $junctionEntry = new $this->junctionModelClass;
                $junctionEntry->load($identity, '');
                $junctionEntry->save();
            }
        }
    }

    public function delete(ActiveRecord $model)
    {
        ($this->junctionModelClass)::deleteAll([$this->modelField => $model->primaryKey]);
    }
}