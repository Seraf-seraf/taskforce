<?php
use yii\widgets\ActiveForm;
use yii\widgets\Menu;
use yii\helpers\Html;
$this->title = 'Настройки аккаунта';
?>
<main class="main-content main-content--left container">
    <div class="left-menu left-menu--edit">
        <h3 class="head-main head-task">Настройки</h3>
        <?php 
            $links = [
                ['label' => 'Мой профиль', 'url' => ['settings/index']],
                ['label' => 'Безопасность', 'url' => ['settings/security']]
            ];
        ?>

        <?= Menu::widget([
            'options' => ['class' => 'side-menu-list'],
            'itemOptions' => ['class' => 'side-menu-item'],
            'activeCssClass' => 'side-menu-item--active',
            'linkTemplate' => '<a href="{url}" class="link link--nav">{label}</a>',
            'items' => $links,
        ]); ?> 
    </div>
    <div class="my-profile-form">
        <h3 class="head-main head-regular">Безопасность</h3>
        <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($passwordChangeForm, 'currentPassword')->passwordInput(['value' => '']); ?>
            <?= $form->field($passwordChangeForm, 'newPassword')->passwordInput(['value' => '']); ?>
            <?= $form->field($passwordChangeForm, 'newPasswordRepeat')->passwordInput(['value' => '']); ?>

            <?= Html::submitInput('Сохранить', ['class' => 'button button--blue']); ?>
        <?php ActiveForm::end(); ?>

        <?php $form = ActiveForm::begin(); ?>
            <h3 class="head-main head-regular">Настройки приватности</h3>
            <?= $form->field($settings, 'privateContacts')->checkbox(); ?>

            <?= Html::submitInput('Сохранить', ['class' => 'button button--blue']); ?>
        <?php ActiveForm::end(); ?>
    </div>
</main>
