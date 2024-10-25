<?php

namespace dynx\assets;

use Yii;
use yii\web\AssetBundle;

use function PHPUnit\Framework\fileExists;

class PwAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . ''; 
    public function init()
    {
        parent::init();
        if (Yii::$app->language != 'en') {
            $i18n = 'js/i18n/pwswitch-' . Yii::$app->language . '.js'; // dynamic file added
            if (file_exists($this->sourcePath . "/" . $i18n))  $this->js[] = $i18n;
        }
    }
    public $css = [
        'css/pw.css',
        'https://use.fontawesome.com/releases/v5.15.1/css/all.css',
    ];
    public $js = [
        'js/pwswitch.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
        'dynx\assets\DynxAsset'
    ];
}
