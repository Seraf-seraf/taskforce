<?php

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Menu;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge'], 'X-UA-Compatible');
?>

<?php
$this->beginPage(); ?>
<!DOCTYPE html>
    <html lang='<?= Yii::$app->language; ?>'>
<head>
    <base href="/">
    <title><?= Html::encode($this->title); ?></title>
    <?php
    $this->head(); ?>
</head>
<body>
<?php
$this->beginBody(); ?>
<header class="page-header">
    <nav class="main-nav">
        <a href='#' class="header-logo">
            <img class="logo-image" src="img/logotype.png" width=227 height=60 alt="taskforce">
        </a>
        <div class="nav-wrapper">
            <?php
            $links = [
                ['label' => 'Новое', 'url' => ['tasks/index']],
                ['label' => 'Мои задания', 'url' => ['tasks/my']],
                ['label' => 'Создать задание', 'url'   => ['tasks/create']],
                ['label' => 'Настройки', 'url' => ['user/settings']]
            ];

            if (Yii::$app->user->identity->isPerformer ?? 0) {
                $links = [
                    ['label' => 'Новое', 'url' => ['tasks/index']],
                    ['label' => 'Мои задания', 'url' => ['tasks/my']],
                    ['label' => 'Настройки', 'url' => ['user/settings']]
                ];
            }

            if (Yii::$app->controller->id !== 'auth'): ?>
                <?= Menu::widget([
                    'options'        => ['class' => 'nav-list'],
                    'itemOptions'    => ['class' => 'list-item'],
                    'activeCssClass' => 'list-item--active',
                    'linkTemplate'   => '<a href="{url}" class="link link--nav">{label}</a>',
                    'items'          => $links,
                ]); ?>
            <?php endif; ?>
        </div>
    </nav>
    <?php
    if (Yii::$app->controller->id !== 'auth'): ?>
        <?php if ($user = Yii::$app->user->identity): ?>
            <div class="user-block">
                <a href="<?= Url::toRoute([
                    'user/view', 'id' => $user->id,
                ]); ?>">
                    <img class="user-photo"
                         src="<?= $user->userSettings->avatar; ?>" width="55"
                         height="55"
                         alt="Аватар">
                </a>
                <div class="user-menu">
                    <p class="user-name"><?= $user->name; ?></p>
                    <div class="popup-head">
                        <ul class="popup-menu">
                            <li class="menu-item">
                                <a href="<?= Url::toRoute('user/settings'); ?>"
                                   class="link">Настройки</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="link">Связаться с нами</a>
                            </li>
                            <li class="menu-item">
                                <a href="<?= Url::toRoute('auth/logout'); ?>"
                                   class="link">Выход из системы</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</header>

<?= $content ?? '' ?>

<?php
$this->endBody(); ?>
</body>
</html>
<?php
$this->endPage(); ?>