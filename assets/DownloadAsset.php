<?php

namespace h3tech\crud\assets;

use yii\web\JqueryAsset;

class DownloadAsset extends Asset
{
    public $js = ['js/download.js'];

    public $depends = [
        JqueryAsset::class,
    ];
}
