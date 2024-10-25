<?php

namespace dynx\models;

use Yii;
use yii\base\Model;
use dynx\Module;
use dynx\models\User;
use yii\bootstrap5\Html;

class UserForm extends Model
{
    public $name;
    public $email;
    public $password;
    public $password2;
    public $pin;

    private $_user=null;


    public function rules()
    {
        $rules = array(
            //LOGIN
            [['email', 'password'], 'required', 'on' => 'login'],
            ['password', 'validatePassword', 'on' => 'login'],
            //PIN read
            ['pin','safe'],
            [['pin','email'],'required',"on"=>"setpin"],
            ['pin', 'validatePin', 'on' => 'setpin'],
            //RECOVER
            ['email', 'required', 'on' => 'recover'],
            ['email', 'email', 'on' => 'recover'],

            //CHANGE
            [['password', 'password2'], 'required', 'on' => 'change'],
             ['password2', 'compare', 'compareAttribute' => 'password', 'on' => 'change', 'message' => Yii::t('dynx/ar', "Passwords are different.")],

            //REGISTRATION
            [['email', 'name'], 'required', 'on' => 'registration'],
            ['email', 'email', 'on' => 'registration'],

        );
        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('dynx/ar', 'Name'),
            'password' => Yii::t('dynx/ar', 'Password'),
            'password2' => Yii::t('dynx/ar', 'Repeat Password'),
            'email' => Yii::t('dynx/ar', 'Email'),
            'pin' => Yii::t('dynx/ar', 'Pin'),

        ];
    }
        /**
     * Validates the pin.
     * This method serves as the inline validation for pin code.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePin($attribute, $params)
    {
        if (!$this->hasErrors()) {
            
            $user = $this->getUser();
            if($user)
            Yii::debug("Password ($this->password):validation on".$user->email.($user->isPasswordValid($this->password)?"VALID":"WRONG"),'UserManagement');
            if (!$user || $user->pin!=$this->pin) {
                $this->addError($attribute, Yii::t('dynx/ar', 'Pin is not valid.'));
            }
        }

       
    }
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->attemptValidation()) {
            $this->addError('password', Yii::t('dynx/ar', 'Too many attempts', ['suspend' => $this->getSuspend()]));

            return false;
        }
        if (!$this->hasErrors()) {
            
            $user = $this->getUser();
            if($user)
            Yii::debug("Password ($this->password):validation on".$user->email.($user->isPasswordValid($this->password)?"VALID":"WRONG"),'UserManagement');
            if (!$user || !$user->isPasswordValid($this->password)) {
                $this->addError($attribute, Yii::t('dynx/ar', 'Incorrect username or password.'));
            }
        }

       
    }
    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            Yii::debug('Login:'.$this->scenario,'UserManagement');
            return Yii::$app->user->login($this->getUser(),  0);
        }
        return false;
    }

    /** Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if (is_null($this->_user)) {
            $this->_user = User::findByEmail($this->email);
        }
        
        return $this->_user;
    }

    public function getSuspend()
    {
        $mod = Module::getInstance();
        $lastAttempt = Yii::$app->session->get("dy_attempt_last");
        return date('i:s', (($lastAttempt + $mod->attemptTimeout)) - time());
    }
    /**
     * Check how much attempts user has been made in X seconds
     *
     * @return bool
     */
    public function attemptValidation()
    {
        $lastAttempt = Yii::$app->session->get("dy_attempt_last");

        if ($lastAttempt) {
            $attempts = Yii::$app->session->get("dy_attempt_count", 1);
            $mod = Module::getInstance();
            if ($attempts > $mod->attempt) {
                if (($lastAttempt + $mod->attemptTimeout) < time()) {
                    Yii::$app->session->set("dy_attempt_count", 1);
                    return true;
                }
                return false;
            } else {
                Yii::$app->session->set("dy_attempt_last", time());
                Yii::$app->session->set("dy_attempt_count", ++$attempts);
                return true;
            }
        } else {

            Yii::$app->session->set("dy_attempt_last", time());
            Yii::$app->session->set("dy_attempt_count", 1);

            return true;
        }
    }


}
