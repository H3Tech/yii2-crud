<?php

namespace h3tech\crud\helpers;

use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;

class SearchHelper
{
    public static function getTimeFilterInterval(ActiveRecord $searchModel, $params, $sourceAttribute)
    {
        $interval = null;

        $modelName = StringHelper::baseName(get_class($searchModel));

        $timeString = isset($params[$modelName][$sourceAttribute]) ? $params[$modelName][$sourceAttribute] : '';
        if (preg_match('/([^_]+)_([^_]+)/', $timeString, $matches)) {
            $interval = ['from' => $matches[1], 'to' => $matches[2]];
        }

        return $interval;
    }

    public static function applyTimeFilters(ActiveRecord $searchModel, ActiveQuery $query, array $params,
                                            array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (is_numeric($key)) {
                $sourceAttribute = $targetAttribute = $value;
            } else {
                $sourceAttribute = $key;
                $targetAttribute = $value;
            }

            $timeFilter = [];
            if (($interval = static::getTimeFilterInterval($searchModel, $params, $sourceAttribute)) !== null) {
                $timeFilter = ['between', $targetAttribute, $interval['from'], $interval['to']];
            }
            if (empty($timeFilter)) {
                $query->andFilterWhere([$targetAttribute => $searchModel->$sourceAttribute]);
            } else {
                $query->andFilterWhere($timeFilter);
            }
        }
    }

    public static function addFieldsToSort(ActiveRecord $searchModel, ActiveDataProvider $dataProvider,
                                           $fields)
    {
        $modelClass = get_parent_class($searchModel);
        /** @var ActiveRecord $model */
        $model = new $modelClass();

        foreach ($fields as $key => $field) {
            $attribute = is_numeric($key) ? $field : $key;

            $dataProvider->sort->attributes[$attribute] = [
                'asc' => [$field => SORT_ASC],
                'desc' => [$field => SORT_DESC],
                'label' => $model->getAttributeLabel($attribute),
            ];
        }
    }
}
