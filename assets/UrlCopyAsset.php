<?php

namespace h3tech\crud\assets;

use yii\web\JqueryAsset;

class UrlCopyAsset extends Asset
{
    public $js = [
        'js/clipboard.min.js',
        'js/url-copy.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
