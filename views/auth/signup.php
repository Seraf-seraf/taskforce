<?php
/** @var \app\controllers\AuthController $model */

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
            <?= $form->field($model, 'name')->textInput(
                ['autocomplete' => 'off']
            ); ?>
            <div class="half-wrapper">
                <?= $form->field($model, 'email')->textInput(
                    ['autocomplete' => 'off']
                ); ?>
                <?= $form->field($model, 'city_id')->dropDownList(
                    array_column($cities, 'name', 'id')
                );
                ?>
            </div>
            <div class="half-wrapper">
                <?= $form->field($model, 'password')->passwordInput(); ?>
            </div>
            <div class="half-wrapper">
                <?= $form->field($model, 'password_repeat')->passwordInput(); ?>
            </div>
            <div class="half-wrapper">
                <?= $form->field($model, 'isPerformer')->checkbox(); ?>
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