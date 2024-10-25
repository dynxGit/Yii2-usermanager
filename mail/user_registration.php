<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user dynx\models\User */

/**
 * OPTIONS from User->setEmailOptions()
 */
/* @var $tokenlink string User token link  */
/* @var $pinHtmlCode string User pin HTML form code  */

?>
<div class="confirm-email">
    <p><?= Yii::t('dynx/email', 'Hello {username},', [
            'username' => $user->name
        ]) ?></p>
<?=$pinHtmlCode?>
    <p><?= Yii::t('dynx/email', 'Follow the link below to verify your email:') ?></p>

    <p><?= Html::a(Html::encode($tokenlink), $tokenlink) ?></p>
</div>