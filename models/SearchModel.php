<?php

namespace h3tech\crud\models;

use yii\data\ActiveDataProvider;
use yii2tech\ar\search\ActiveSearchModel;

class SearchModel extends ActiveSearchModel
{
    public function search($params)
    {
        $dataProvider = parent::search($params);
        $this->customizeSearch($dataProvider, $params);
        return $dataProvider;
    }

    public function customizeSearch(ActiveDataProvider $dataProvider, array $params)
    {
    }
}
