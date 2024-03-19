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
                [
                    'label' => 'Мои задания',
                    'url' => ['tasks/my'],
                    'active' => Yii::$app->controller->route === 'tasks/my'
                ],
                [
                    'label' => 'Создать задание',
                    'url' => ['tasks/create'],
                    'visible' => isset(Yii::$app->user->identity->isPerformer) ? !Yii::$app->user->identity->isPerformer : false
                ],
                [
                    'label' => 'Настройки',
                    'url' => ['settings/index'],
                    'active' => Yii::$app->controller->id === 'settings'
                ]
            ];

            if (!Yii::$app->user->isGuest): ?>
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
                <?= Html::a(
                    Html::img(
                        $user->userSettings->avatar,
                        [
                            'class' => "user-photo",
                            'width' => "55",
                            'height' => "55",
                            'alt' => "Аватар"
                        ]
                    ),
                    Url::toRoute(
                        [
                            'user/view',
                            'id' => $user->id
                        ]
                    ),
                    [
                        'class' => 'header__account-registration'
                    ]
                );
                ?>
                <div class="user-menu">
                    <p class="user-name"><?= Html::encode($user->name); ?></p>
                    <div class="popup-head">
                        <?php
                        $items = [
                            ['label' => 'Настройки', 'url' => Url::toRoute('settings/index')],
                            ['label' => 'Выход из системы', 'url' => Url::toRoute('auth/logout')]
                        ];
                        ?>

                        <?= Html::ul($items, [
                            'class' => 'popup-menu',
                            'item' => function ($item, $index) {
                                return Html::tag(
                                    'li',
                                    Html::a($item['label'], $item['url'], ['class' => 'link']),
                                    ['class' => 'menu-item']
                                );
                            }
                        ]) ?>
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