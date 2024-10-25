<?php

namespace dynx\assets;

use yii\web\AssetBundle;

class PinAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . '';
    public $cssCompressor = 'yui-compressor --type css {from} -o {to}';
    public $css = [
        'css/pin.css'
    ];
    public $js = [
        'js/pin.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
        'dynx\assets\dynxAsset'
    ];
}
