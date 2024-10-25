<?php

namespace dynx\models\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use dynx\Module;
use Yii;

class UserEmailBehavior extends Behavior
{
    /**
     * @param $subject
     * @param $view
     * @param array $options
     * @return bool
     */
    public function sendEmail($subject, $view, $options = [])
    {
        $mailView = [
            'html' => "$view-html",
            'text' => "$view-text",
        ];
        $from = Yii::$app->params['supportEmail'] ?? Yii::$app->params['adminEmail'];
        $options['user'] = $this;

        $mailer = Yii::$app->mailer;
        foreach (Yii::$app->controller->module->mailOptions as $key => $value) {
            $mailer->$key = $value;
        }
        return $mailer
            ->compose($mailView, $options)
            ->setFrom([$from => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject($subject)

            ->send();
    }

    /**
     * @param $subject
     * @param $view
     * @param array $options
     * @param $linkAction string - the action part in the link; if null, $view is taken
     * @return bool
     * @throws \yii\base\Exception
     */
    public function sendTokenEmail($subject, $view, $options = [], $linkAction = null)
    {
        $this->generateToken();
        $this->generatePin();
        if (is_null($linkAction)) $linkAction = $view;
        $module = Yii::$app->controller->module;

        $options['link'] = Yii::$app->urlManager->createAbsoluteUrl([$module->id . '/default/' . $linkAction, 'token' => $this->token]);

        return $this->sendEmail($subject, $view, $options);
    }
}
