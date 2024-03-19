<?php
/** @var \app\models\LoginForm $model */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<section class="modal enter-form form-modal" id="enter-form">
    <h2>Вход на сайт</h2>
    <div style="padding-bottom: 20px; font-size: smaller;">
        <label>
            Если вы впервые на сайте, перейдите на страницу
            <?= Html::a('регистрации', Url::toRoute(['auth/signup'])); ?>
        </label>
    </div>

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true, 'action' => ['auth/login'],
    ]); ?>

    <?= $form->field($model, 'email',
        ['options' => ['class' => 'form-modal-description']])
             ->textInput([
                 'autocomplete' => 'off',
                 'class'        => 'enter-form-email input input-middle',
             ]) ?>

    <?= $form->field($model, 'password',
        ['options' => ['class' => 'form-modal-description']])
        ->passwordInput(['class' => 'enter-form-password input input-middle']); ?>

    <?= Html::submitInput('Войти', ['class' => 'button']) ?>
    <h4>Войти через:</h4>
    <?= yii\authclient\widgets\AuthChoice::widget([
        'baseAuthUrl' => ['auth/auth'],
        'popupMode' => false,
    ]); ?>
    <?php
    ActiveForm::end(); ?>

    <button class="form-modal-close" type="button">Закрыть</button>
</section>