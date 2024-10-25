<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */


use dynx\assets\PwAsset;
use dynx\Module;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = Yii::t('dynx/views', 'Login');
//$this->params['breadcrumbs'][] = $this->title;
?>
<section class="row justify-content-center align-items-center">
    <div class=' col-11 col-md-6 center-screen'>

        <div class="user-form">
            <h1><?= Html::encode($this->title) ?></h1>

            <p><?= Yii::t('dynx/views', 'Please fill out the following fields to login:') ?></p>

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
            <?php

            $module = Module::getInstance();
            PwAsset::register($this);; ?>
            <?= $form->field($model, 'password')->passwordInput([
                'class' => "form-control form-control-lg pwswitched ",
                'placeholder' => $model->getAttributeLabel('password'),
                "data-validator" => $module->passwordValidator
            ]) ?>
            <div class="form-group">
                <div>
                    <?= Html::submitButton(Yii::t('dynx/views', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </div>
            <hr>
            <?= Html::a(Yii::t('dynx/views', 'Recover'), ["/user/recover"]) ?>
            <?= Html::a(Yii::t('dynx/views', 'Registration'), ["/user/registration"]) ?>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</section>