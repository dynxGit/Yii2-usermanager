<?php

namespace dynx\assets;

use Yii;
use yii\web\AssetBundle;

use function PHPUnit\Framework\fileExists;

class DynxAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . '';
    public function init()
    {
    }
    public $css = [
        'css/user.css',
        'https://use.fontawesome.com/releases/v5.15.1/css/all.css',
        '//fonts.googleapis.com/css?family=Roboto+Flex:opsz,wght@8..144,100..1000&display=swap"',
    ];
    public $js = [
       // 'js/pwswitch.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset'
    ];
}
