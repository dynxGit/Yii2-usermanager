<?php

/**
 * YII2 usermanager 
 * ----------
 * User management module for Yii2 framework
 * Version 1.0.0
 * Copyright (c) 2024
 * András Szincsák, Győr Hungary
 * MIT License
 * https://github.com/dynxGit/Yii2-usermanager
 */


namespace dynx;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module as YiiModule;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\web\Application as WebApplication;
use yii\web\GroupUrlRule;
use yii\web\UserEvent;
use yii\helpers\ArrayHelper;
use dynx\models\User;
use dynx\assets\DynxAsset;

/**
 * user module definition class
 */
class Module extends YiiModule implements BootstrapInterface
{
    /**
     * Details of te setting is written in config/config.php 
     */
    public $SUAemail = null;
    public $senderName = "";
    public $mailOptions = [];
    public $attempt = 5;
    public $attemptTimeout = 1 * 60; // 1 minutes
    public $tokenExpired = 3600; // 1 hour
    public $pinFormat = "3C-4N";
    public $pinCss = [];
    public $loginInput = "email";
    /**
     * Password validator params
     *
     * @var array
     */
    public $passwordValidator = [ //TODO set defaults!
        /*Minimum length of password*/
        "length" => 6,
        /* Uppercase character*/
        "upper" => 1,
        /* Numeric character*/
        "number" => 1,
        /* symbol character*/
        "symbol" => 0
    ];
    public $tryout = 30; //days
    public $config = [];

    public $controllerNamespace = 'dynx\controllers';


    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (! Yii::$app->has('authManager')) {
            throw new InvalidConfigException('$app::authManager is not configured.');
        }
        parent::init();
        Yii::configure($this, require __DIR__ . '/config/config.php');
        DynxAsset::register(Yii::$app->view);

        if (!isset(Yii::$app->i18n->translations['dynx/*'])) {
            Yii::$app->i18n->translations['dynx/*'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@dynx/messages/',
                'fileMap' => [
                    'dynx/ar' => 'models.php',
                    'dynx/form' => 'forms.php',
                    'dynx/email' => 'emails.php',
                    'dynx/views' => 'views.php',

                ],
            ];
        }
    }

    /**
     * @param $event  UserEvent
     */
    public static function beforeLogin($event)
    {
        /* @var $user User */
        $user = $event->identity;
        if ($user->status != User::STATUS_ACTIVE)
            $event->isValid = false;  // holds blocked user
        else {
            $user->touch('lastlogin_at');
            $user->updateCounters(['login_count' => 1]);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidConfigException
     */
    public function bootstrap($app)
    {
        if ($app instanceof WebApplication) {
          
            /*
            $rules = new GroupUrlRule([
                'prefix' => $this->id,
                'rules' => [
                    '<a:(confirm|recover)>/<token:[A-Za-z0-9_-]+>' => 'default/<a>',
                    '<a:[\w\-]+>/<id:\d+>' => 'default/<a>',
                    '<c:[\w\-]+>/<a:[\w\-]+>/<id:[\w\-]+>' => '<c>/<a>',
                    '<a:[\w\-]+>' => 'default/<a>',
                    'user/<a:[\w\-]+>' => 'default/<a>',
                ]
            ]);
            $app->getUrlManager()->addRules([$rules], false);
*/
            $app->on($app::EVENT_BEFORE_ACTION, [$this, 'beforeAction']);
        } else {
            /* @var $app ConsoleApplication */
            $app->controllerMap = ArrayHelper::merge($app->controllerMap, [
                'migrate-dynx' => [
                    'class' => '\yii\console\controllers\MigrateController',
                    'migrationPath' => null,
                    'migrationNamespaces' => [
                        'dynx\migrations'
                    ]
                ],
                //  'dynx-DB' => 'dynx\commands\DynxController'
            ]);
        }
    }


    public function getConfigField($fieldname)
    {
        return (isset($this->config[$fieldname])) ? $this->config[$fieldname] : null;
    }

    /**
     * @param $event ActionEvent
     */
    public function beforeAction($event)
    {
        $request = Yii::$app->request;
        $lang = $request->get('lang');
        if ($lang) {
            Yii::$app->session->set('language', $lang);
        } else {
            $lang = Yii::$app->session->get('language');
        }
        if (!Yii::$app->user->isGuest)
            $lang = Yii::$app->user->identity->lang;
        if ($lang)
            Yii::$app->language = $lang;
        return parent::beforeAction($event);
    }


    public function __get($name)
    {
        if (substr($name, 0, 3) === 'cfg') {
            $attribute = lcfirst(substr($name, 3));
            return (isset($this->config[$attribute])) ? $this->config[$attribute] : null;
        }
        return parent::__get($name);
    }
}
