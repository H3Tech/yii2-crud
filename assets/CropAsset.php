<?php

namespace h3tech\crud\assets;

use yii\bootstrap\BootstrapPluginAsset;

class CropAsset extends Asset
{
    public $css = [
        'css/crop.css',
    ];

    public $js = [
        'js/jquery.initialize.min.js',
        'js/crop.js',
    ];

    public $depends = [
        BootstrapPluginAsset::class,
        JCropAsset::class,
        FontAwesomeAsset::class,
    ];
}
