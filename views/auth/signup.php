<?php
/** @var \app\controllers\AuthController $user */

/** @var \app\controllers\AuthController $cities */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Регистрация';
?>

<main class="main-content container">
    <div class="center-block">
        <div class="registration-form regular-form">
            <?php
            $form = ActiveForm::begin(['enableAjaxValidation' => true]); ?>
            <h3 class="head-main head-task">Регистрация нового пользователя</h3>
            <?= $form->field($user, 'name')->textInput(
                ['autocomplete' => 'off']
            ); ?>
            <div class="half-wrapper">
                <?= $form->field($user, 'email')->textInput(
                    ['autocomplete' => 'off']
                ); ?>
                <?= $form->field($user, 'city_id')->dropDownList(
                    array_column($cities, 'name', 'id')
                );
                ?>
            </div>
            <div class="half-wrapper">
                <?= $form->field($user, 'password')->passwordInput(); ?>
            </div>
            <div class="half-wrapper">
                <?= $form->field($user, 'password_repeat')->passwordInput(); ?>
            </div>
            <div class="half-wrapper">
                <?= $form->field($user, 'isPerformer')->checkbox(); ?>
            </div>
            <h4>Создать аккаунт с помощью: </h4>
            <div class="half-wrapper">
                <?= yii\authclient\widgets\AuthChoice::widget([
                    'baseAuthUrl' => ['auth/auth'],
                    'popupMode' => false,
                ]); ?>
            </div>
            <?= Html::submitInput(
                'Создать аккаунт',
                [
                    'class' => 'button button--blue',
                ]
            ); ?>
            <?php
            ActiveForm::end(); ?>
        </div>
    </div>
</main>