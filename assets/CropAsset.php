<?php

namespace h3tech\crud\assets;

use yii\bootstrap\BootstrapPluginAsset;
use yii\web\AssetBundle;

class CropAsset extends AssetBundle
{
    public $sourcePath = '@h3tech/crud/web';

    public $css = [
        'css/crop.css',
    ];

    public $js = [
        'js/crop.js',
    ];

    public $depends = [
        BootstrapPluginAsset::class,
        JCropAsset::class,
        FontAwesomeAsset::class,
    ];
}
