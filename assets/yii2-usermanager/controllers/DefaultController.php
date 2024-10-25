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

namespace dynx\controllers;

use dynx\components\DyController;
use Yii;
use yii\base\InvalidCallException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use dynx\Module;
use dynx\models\User;
use dynx\models\UserForm;


/**
 */
class DefaultController extends DyController
{
    /* public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'settings', 'pw-change', 'delete', 'download'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Login by email and password
     * User can register and revover
     * Succesfully login redirect to landing page (Home)
     * 
     * @return mixed
     */
    public function actionLogin()
    {

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new UserForm(['scenario' => 'login']);

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Simple logout user
     * Redirecting to hame page
     * 
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * User Registration
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionRegistration()
    {
        $view = "registration";

        $model = new User([
            'scenario' => 'create',
            'status' => User::STATUS_PENDING,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->sendEmail(
                'user_registration',
                Yii::t("dynx/email", 'Registration on {appname}', ['appname' => Yii::$app->name])
            )) {
                Yii::$app->session->setFlash(
                    'success',
                    Yii::t('dynx/views', 'Thank you for registering. Please check your inbox (at <b>{email}</b>) for a verification email.', ['email' => $model->email])
                );
                $model->scenario = 'setpin';
                $view = "activation";
            }
        }

        return $this->render($view, [
            'model' => $model,
            'user' => $model,
        ]);
    }
        /**
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionActivate_pin($token)
    {
        /* @var $module Module */
        $module = $this->module;
        $model = new UserForm();
        $user = User::findByToken($token, User::STATUS_PENDING);  
        
        $view = "activation";
        if (!$user) {
                        Yii::$app->session->setFlash(
                'error',
                Yii::t('dynx/form', 'Your token is invalid. Ask for new activation code!') 
            );
            return  $this->redirect('recover?token=' . $token);
        } else {
            $user->scenario = 'setpin';
            $user->generateToken();
            if ($user->load(Yii::$app->request->post()) && $user->save()) {

                Yii::$app->session->setFlash(
                    'success',
                    Yii::t('dynx/views', 'The validatation is succeded. Please add your password, following the rules!')
                );
                return  $this->redirect('changepassword?token=' . $token);
            }
        }

        return $this->render($view, [
            'model' => $model,
            'user' => $user
        ]);
    }
        /**
     * Forgotten password - password setting from email
     *
     * @param string $token
     * @var User $user
     * @return void
     */
    public function actionChangepassword($token)
    {
        $user = User::findByToken($token, User::STATUS_PENDING);  
     
   //     if (!$user)
   //         return  $this->redirect('login');
        $model = new UserForm(['scenario' => 'change']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user->password = trim($model->password);
            $user->scenario = 'update';
            if ($user->save()) {
                Yii::$app->user->login($user,  0);
                return $this->goHome();
            }
        } else {
            $model->password = '';
        }
        return $this->render('changepassword', [
            'model' => $model,
            'user' => $user
        ]);
    }
    /**
     * Recover Account- sending pin via email
     *
     * @param string $token
     * @return void
     */
    public function actionRecover()
    {
        $model = new UserForm(['scenario' => 'recover']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findByEmail($model->email);
            $user->scenario = 'update';
            $user->generateToken();
            $user->save();
            if ($user->sendEmail(
                'user_registration',
                Yii::t("dynx/email", 'Registration on {appname}', ['appname' => Yii::$app->name]),
                ['text' => "TEXT"]
            ))
                $this->redirect('activate_pin?token=' . $user->token);
        } else {
            $model->password = '';
        }
        return $this->render('recover', [
            'model' => $model,
        ]);
    }



    public function actionValidate($token)
    {
        $view = "activation_falled";
        $user = User::findByToken($token, User::STATUS_PENDING);
        if ($user) {
            $view = "activation_falled";
        }
        return $this->render($view, [
            'user' => $user,
        ]);
    }

    /**
     * @param string $token
     * @return yii\web\Response
     */
    public function actionConfirm($token)
    {
        return $this->tokenAction(
            $token,
            Yii::t('pluto', 'Your registration has been confirmed. You are now logged in.'),
            Yii::t('pluto', 'Sorry, we are unable to verify your registration.')
        );
    }

    /**
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionForgot()
    {
        /* @var $module Module */
        $module = $this->module;

        $model = new UserForm([
            'flags' => $module->getPwFlags('forgot')
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findByEmail($model->email);
            if ($user) {
                $user->generateToken();
                if ($user->save() && $user->sendTokenEmail(Yii::t('pluto', 'Password reset for {appname}', [
                    'appname' => Yii::$app->name
                ]), 'recover')) {
                    Yii::$app->session->setFlash(
                        'success',
                        Yii::t('pluto', 'Please check your inbox for further instructions.')
                    );
                    return $this->goHome();
                }
            } else {
                Yii::$app->session->setFlash(
                    'error',
                    Yii::t('pluto', 'Sorry, we are unable to reset the  password related to this email address.')
                );
            }
        }

        return $this->render('forgot', [
            'model' => $model,
        ]);
    }

    /**
     * @param string $token
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionRecoverOLD($token)
    {
        /* @var $module Module */
        $module = $this->module;

        $model = User::findByToken($token);

        if ($model) {
            $model->scenario = 'new-pw';
            $model->flags = $module->getPwFlags('recover');

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->removeToken();

                if ($model->save(false)) {
                    if (Yii::$app->user->login($model)) {
                        Yii::$app->session->setFlash(
                            'success',
                            Yii::t('pluto', 'New password saved. You are now logged in.')
                        );

                        return $this->goHome();
                    }
                }
            }
            return $this->render('recover', [
                'model' => $model,
            ]);
        }
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionResend()
    {
        /* @var $module Module */
        $module = $this->module;

        $model = new UserForm([
            'scenario' => 'resend',
            'status' => User::STATUS_PENDING,
            'flags' => $module->getPwFlags('resend')
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findByEmail($model->email, User::STATUS_PENDING);
            if ($user && $user->sendTokenEmail(Yii::t('pluto', 'Account registration at {appname}', [
                'appname' => Yii::$app->name
            ]), 'confirm')) {
                Yii::$app->session->setFlash(
                    'success',
                    Yii::t('pluto', 'Please check your inbox for a verification email.')
                );
                return $this->goHome();
            }
            Yii::$app->session->setFlash(
                'error',
                Yii::t('pluto', 'Sorry, we were unable to resend a verification email to this email address.')
            );
        }

        return $this->render('resend', [
            'model' => $model,
        ]);
    }

    /**
     * User settings.
     *
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionSettings()
    {
        /* @var $module Module */
        $module = $this->module;

        $user = $this->getCurrentUser();
        $user->scenario = 'settings';
        $user->flags = $module->getPwFlags('settings');

        $req = Yii::$app->request;
        if ($req->isGet) Url::remember($req->referrer);

        if ($user->load($req->post())) {
            $emailChanged = $user->isAttributeChanged('email');
            if ($emailChanged) {
                $user->status = User::STATUS_PENDING;
                $user->generateToken();
            }
            if ($user->save()) {
                if ($emailChanged) {
                    $user->sendTokenEmail(Yii::t('pluto', 'Confirmation email changed at {appname}', [
                        'appname' => Yii::$app->name
                    ]), 'confirm');
                    Yii::$app->session->setFlash(
                        'success',
                        Yii::t('pluto', 'Please check your inbox for a verification email.')
                    );
                } else Yii::$app->session->setFlash('success', Yii::t('pluto', 'Settings saved.'));
                return $this->goBack();
            }
        }

        return $this->render('settings', [
            'model' => $user,
        ]);
    }

    /**
     * @param string $token
     * @return yii\web\Response
     */
    public function actionEmailChanged($token)
    {
        return $this->tokenAction(
            $token,
            Yii::t('pluto', 'Your email has been confirmed. Settings saved.'),
            Yii::t('pluto', 'Sorry, we are unable to verify your email.')
        );
    }

    /**
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionPwChange()
    {
        /* @var $module Module */
        $module = $this->module;

        $flags = $module->getPwFlags('pw-change');
        $user = $this->getCurrentUser();
        $user->scenario = 'new-pw';
        $user->flags = $flags;

        $model = new UserForm([
            'user' => $user,
            'flags' => $flags
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($user->save(false)) Yii::$app->session->setFlash(
                'success',
                Yii::t('pluto', 'New password saved.')
            );
            else  Yii::$app->session->setFlash(
                'error',
                Yii::t('pluto', 'Sorry, we were unable to change your password.')
            );
            return $this->goBack();
        }

        return $this->render('pw_change', [
            'model' => $model,
        ]);
    }

    /**
     * @return mixed
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete()
    {
        /* @var $module Module */
        $module = $this->module;
        $pwHint = $module->passwordHint;

        $model = $this->getCurrentUser();
        $model->scenario = 'delete';
        $model->flags = $module->getPwFlags('delete');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->user->logout();
            $model->delete();

            return $this->goHome();
        }

        return $this->render('delete', [
            'model' => $model,
            'pwHint' => $pwHint
        ]);
    }

    /**
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDownload()
    {
        /* @var $module Module */
        $module = $this->module;
        $pwHint = $module->passwordHint;

        $model =  $this->getCurrentUser();
        $model->scenario = 'settings';
        $model->flags = $module->getPwFlags('download');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $attrs = $model->getAttributes(null, ['id', 'auth_key', 'password_hash', 'token', 'status']);
            $attrs['statusText'] = $model->statusText;
            $csv = [];
            foreach ($attrs as $key => $value) {
                $label = $model->getAttributeLabel($key);
                $csv[] = "$label = $value";
            }
            $roles = implode(', ', $model->roles);
            $csv[] = "Role(s) = $roles";
            $appName = Yii::$app->name;
            $now = date('Y-m-d H:i:s');
            $line = str_repeat('=', 32);
            $content = "User data at $appName\r\n$line\r\nGenerated at $now\r\n\r\n" . implode("\r\n", $csv);
            return Yii::$app->response->sendContentAsFile($content, "$appName.txt", [
                'mimeType' => 'text/plain',
            ]);
        }

        return $this->render('download', [
            'model' => $model,
            'pwHint' => $pwHint
        ]);
    }

    /**
     * @param $token
     * @param $successFlash
     * @param $errorFlash
     * @return \yii\web\Response
     */
    protected function tokenAction($token, $successFlash, $errorFlash)
    {
        $user = User::findByToken($token, User::STATUS_PENDING);
        if ($user) {
            $user->removeToken();
            $user->status = User::STATUS_ACTIVE;
            if ($user->save(false)) {
                if (Yii::$app->user->login($user)) {
                    Yii::$app->session->setFlash('success', $successFlash);
                    return $this->goHome();
                }
            }
        }

        Yii::$app->session->setFlash('error', $errorFlash);
        return $this->goHome();
    }

    /**
     * @return User|null
     */
    protected function getCurrentUser()
    {
        $user = Yii::$app->user;
        if ($user->isGuest) {
            throw new InvalidCallException(Yii::t('pluto', 'User is not logged in.'));
        }
        /* @var $r User */
        $r = $user->identity;
        return $r;
    }
}
