<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use dynx\assets\PwAsset;
use dynx\Module;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Json;

$this->title = Yii::t('dynx/views', 'Change Password');
//$this->params['breadcrumbs'][] = $this->title;
?>
<section class="row justify-content-center align-items-center">
    <div class=' col-12 col-md-10 col-lg-6 center-screen'>

        <div class="user-form">
            <h1><?= Html::encode($this->title) ?></h1>
            <p><?= Yii::t('dynx/views', 'Please set your password following displayed rules:') ?></p>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'col-lg-3 col-form-label mr-lg-3'],
                    'inputOptions' => ['class' => 'col-lg-3 form-control form-control-lg'],
                    'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                ],
            ]); ?>
            <?php
            $module = Module::getInstance();
            PwAsset::register($this);;
            ?>
            <?= $form->field($model, 'password')->passwordInput([
                'class' => "form-control form-control-lg pwswitched pwvalidate",
                "data-validator" => Json::encode($module->passwordValidator)
            ]) ?>
            <?= $form->field($model, 'password2')->passwordInput() ?>



            <div class="form-group">
                <div>
                    <?= Html::submitButton(Yii::t('dynx/views', 'Save password'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</section>