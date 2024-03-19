<?php

use yii\helpers\BaseStringHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Menu;

$this->title = 'Мои задания';
?>

<main class="main-content container">
    <div class="left-menu">
        <h3 class="head-main head-task">Мои задания</h3>
        <?php
        $links = [
            [
                'label' => 'Новые',
                'url' => ['tasks/my', 'tag' => 'new'],
                'visible' => !Yii::$app->user->identity->isPerformer
            ],
            ['label' => 'В процессе', 'url' => ['tasks/my', 'tag' => 'progress']],
            [
                'label' => 'Просроченные',
                'url' => ['tasks/my', 'tag' => 'expired'],
                'visible' => Yii::$app->user->identity->isPerformer
            ],
            ['label' => 'Закрытые', 'url' => ['tasks/my', 'tag' => 'closed']],
        ];
        ?>

        <?= Menu::widget([
            'options' => ['class' => 'side-menu-list'],
            'itemOptions' => ['class' => 'side-menu-item'],
            'activeCssClass' => 'side-menu-item--active',
            'linkTemplate' => '<a href="{url}" class="link link--nav">{label}</a>',
            'items' => $links
        ]); ?>
    </div>
    <div class="left-column left-column--task">
        <?php
        $title;
        switch ($tag) {
            case 'new':
                $title = 'Новое';
                break;
            case 'progress':
                $title = 'В процессе';
                break;
            case 'expired':
                $title = 'Просроченные';
                break;
            case 'closed':
                $title = 'Закрытые';
                break;
            default:
                $title = 'Все задания';
        }
        ?>
        <h3 class="head-main head-regular">
            <?= $title; ?>
        </h3>
        <?php
        foreach ($tasks as $task): ?>
            <div class="task-card">
                <div class="header-task">
                    <?= Html::a(
                        Html::encode($task->name),
                        Url::toRoute(['tasks/view', 'id' => $task->id]),
                        ['class' => "link link--block link--big"]
                    ); ?>
                    <?= Html::tag(
                        'p',
                        empty($task->budget) ? '' : Html::encode($task->budget) . ' ₽',
                        ['class' => 'price price--task']
                    ); ?>
                </div>
                <p class="info-text">
                    <span class="current-time">
                        <?= Yii::$app->formatter->asRelativeTime($task->dateCreate) ?>
                    </span>
                </p>
                <?= Html::tag(
                    'p',
                    Html::encode(BaseStringHelper::truncate($task->description, 200)) ?? '',
                    ['class' => 'task-text']
                ); ?>
                <div class="footer-task">
                    <?php
                    if ($task->location): ?>
                        <?= Html::tag('p', Html::encode($task->city->name), ['class' => 'info-text town-text']); ?>
                    <?php
                    endif ?>
                    <?= Html::tag(
                        'p',
                        Html::encode($task->category->name),
                        ['class' => 'info-text ' . Html::encode($task->category->icon) . ' category-text']
                    ); ?>
                    <?= Html::a(
                        'Смотреть задание',
                        Url::toRoute(['tasks/view', 'id' => $task->id]),
                        ['class' => 'button button--black']
                    ); ?>
                </div>
            </div>
        <?php
        endforeach ?>
        <div class="pagination-wrapper">
            <?= LinkPager::widget([
                'pagination' => $pages,
                'options' => ['class' => 'pagination-list'],
                'pageCssClass' => 'pagination-item',
                'activePageCssClass' => 'pagination-item--active',
                'prevPageCssClass' => 'pagination-item mark',
                'nextPageCssClass' => 'pagination-item mark',
                'nextPageLabel' => '',
                'prevPageLabel' => '',
                'linkOptions' => ['class' => 'link link--page'],
                'maxButtonCount' => 5,
            ]);
            ?>
        </div>
    </div>
</main>