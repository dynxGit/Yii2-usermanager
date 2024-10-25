<?php

namespace dynx\models\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use dynx\Module;
use dynx\models\User;
use Yii;
use yii\helpers\ArrayHelper;

class UserStatusBehavior extends Behavior
{
    public $statusEmailResonse = null;

    public function events()
    {
        return [
        //    ActiveRecord::EVENT_AFTER_INSERT    => 'setStatus',
            ActiveRecord::EVENT_AFTER_FIND      => 'checkStatus',
            ActiveRecord::EVENT_AFTER_UPDATE    => 'setStatus',
            //       ActiveRecord::EVENT_BEFORE_INSERT    => 'setStatus',
        ];
    }

    public function initStatus($event) {}
    public function checkStatus($event)
    {
        $mod = Module::getInstance();
    }

    public function setStatus($event)
    {
        if (isset($event->changedAttributes['status'])) {
            switch ($this->owner->status) {
                     /* email validated */
                case User::STATUS_VALIDATED:

                    break;
                case User::STATUS_ACTIVE:

                    break;
                case User::STATUS_INACTIVE:

                    break;
            }
            //  }
            echo $this->owner->status;
        }
    }

    /**
     * @param $subject
     * @param $view
     * @param array $options
     * @return bool
     */
    public function sendEmail($view, $subject, $options = [])
    {
        if (Yii::$app instanceof \yii\web\Application) {
            $from = Yii::$app->params['supportEmail'] ?? Yii::$app->params['adminEmail'];
            $mailer = Yii::$app->mailer;
            $module = Module::getInstance();
            $options = ArrayHelper::merge($this->owner->setEmailOptions(), $options);
            foreach ($module->mailOptions as $key => $value) {
                $mailer->$key = $value;
            }
            $result = $mailer
                ->compose($view, $options)
                ->setFrom([$from => ($module->senderName) ? $module->senderName : Yii::$app->params['senderName']])
                ->setTo($this->owner->email)
                ->setSubject($subject)

                ->send();


            return $result;
        }
        return true;
    }
}
