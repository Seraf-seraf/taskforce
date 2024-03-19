<?php
use app\models\TaskCategories;
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
        <h3 class="head-main head-regular">Мой профиль</h3>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            
            <div class="photo-editing">
                <div>
                    <p class="form-label">Аватар</p>
                    <?= Html::img($settings->avatar, ['width' => 83, 'height' => 83, 'alt' => 'аватарка пользователя', 'class' => 'avatar-preview']); ?>
                </div>
                <?= $form->field($settings, 'avatar')->input('file', ['accept' => 'image/*', 'style' => 'display: none'])->label('Сменить аватар', ['class' => 'button button--black']); ?>
            </div>

            <?= $form->field($user, 'name')->textInput(); ?>
        
            <?= $form->field($user, 'city_id')->dropDownList(array_column($cities, 'name', 'id')); ?>
        
            <div class="half-wrapper">
                <?= $form->field($user, 'email')->input('email'); ?>
                <?= $form->field($settings, 'birthday')->input('date'); ?>
            </div>

            <div class="half-wrapper">
                <?= $form->field($settings, 'phone')->input('tel'); ?>
                <?= $form->field($settings, 'telegram')->textInput(); ?>
            </div>
            
            <?= $form->field($settings, 'description')->textArea(); ?>

            <div class="form-group">
                <?= $form->field($settings, 'categories_id')->checkboxList(array_column(TaskCategories::find()->all(), 'name', 'id'), ['class' => 'checkbox-profile', 'itemOptions' => ['labelOptions' => ['class' => 'control-label']]]); ?>
            </div>
            <?= Html::submitInput('Сохранить', ['class' => 'button button--blue']); ?>
        <?php ActiveForm::end(); ?>
    </div>
</main>
<script>
    //отображение превью при загрузке аватарки
    const preview = document.querySelector('.avatar-preview');
    const fileInput = document.querySelector('#usersettings-avatar');

    fileInput.addEventListener('change', () => {
        let file = fileInput.files[0];
        preview.src = URL.createObjectURL(file);
    });
</script>