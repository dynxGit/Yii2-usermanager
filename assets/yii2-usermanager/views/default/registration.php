<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = Yii::t('dynx/views', 'Registration');
//$this->params['breadcrumbs'][] = $this->title;
?>
<section class="row justify-content-center align-items-center">
    <div class=' col-11 col-md-6 center-screen'>

        <div class="user-form">
            <h1><?= Html::encode($this->title) ?></h1>

            <p><?= Yii::t('dynx/views', 'Please fill out the following fields for registration:') ?></p>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'col-lg-3 col-form-label mr-lg-3'],
                    'inputOptions' => ['class' => 'col-lg-3 form-control'],
                    'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                ],
            ]); ?>

            <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>



            <div class="form-group">
                <div>
                    <?= Html::submitButton(Yii::t('dynx/views', 'Signup'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </div>
            <? $form->errorSummary($model) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</section>