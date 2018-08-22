<?php

namespace h3tech\crud\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class JCropAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower-asset/jcrop';

    public $css = [
        'css/jquery.Jcrop.min.css',
    ];

    public $js = [
        'js/jquery.color.js',
        'js/jquery.Jcrop.min.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
