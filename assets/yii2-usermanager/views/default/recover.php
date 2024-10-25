<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */


use dynx\assets\PwAsset;
use dynx\Module;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = Yii::t('dynx/views','Recover account');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    
    <div class="row">
        <div class="col-lg-6 offset-lg-3">
            <p><?=Yii::t('dynx/views','Please give your e-mail address, cshek your mailbox for next steps')?></p>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                //   'layout'=>'floating',
                'options' => ['autocomplete' => 'off'],
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'col-lg-4 col-form-label mr-lg-3'],
                    'inputOptions' => ['class' => 'col-lg-3 form-control form-control-lg', 'autocomplete' => 'list '],
                    'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                ],
            ]); ?>

            <?= $form->field($model, 'email')->textInput([
                'type' => 'email',
                'placeholder' => $model->getAttributeLabel('email'),

                'autofocus' => true
            ]) ?>
        <div class="form-group">
            <div>
                <?= Html::submitButton(Yii::t('dynx/views','Recover account'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    </div>
</div>
</div>