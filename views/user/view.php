<?php
/** @var \app\controllers\UserController $responses */

/** @var \app\controllers\UserController $model */
/** @var \app\controllers\UserController $categories */
/** @var \app\controllers\UserController $error */
/** @var \app\controllers\UserController $comments */

use app\helpers\UIHelper;
use app\models\Performer;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Профиль пользователя';
?>

<main class="main-content container">
    <?php
    if ( ! empty($error)): ?>
        <h1><?= $error->getMessage(); ?></h1>
    <?php
    else: ?>
        <div class="left-column">
            <h3 class="head-main">
                <?= Html::encode($model->name); ?>
            </h3>
            <div class="user-card">
                <div class="photo-rate">
                    <img class="card-photo" src="<?= Html::encode(
                        $model->userSettings->avatar ?? ''
                    ); ?>" width="191" height="190" alt="Фото пользователя">
                    <div class="card-rate">
                        <?= UIHelper::getStarsByRate(
                            $model->rating->userRating,
                            'big'
                        ) ?>
                        <span class="current-rate">
                        <?= Html::encode($model->rating->userRating); ?>
                    </span>
                    </div>
                </div>
                <?php
                if ( ! empty($model->userSettings->description)): ?>
                    <p class="user-description">
                        <?= Html::encode($model->userSettings->description); ?>
                    </p>
                <?php
                endif; ?>
            </div>
            <div class="specialization-bio">
                <div class="specialization">
                    <p class="head-info">Специализации</p>
                    <ul class="special-list">
                        <?php
                        foreach ($categories as $category): ?>
                            <li class="special-item">
                                <a href="#" class="link link--regular">
                                    <?= Html::encode($category); ?>
                                </a>
                            </li>
                        <?php
                        endforeach; ?>
                    </ul>
                </div>
                <div class="bio">
                    <p class="head-info">Био</p>
                    <p class="bio-info">
                        <span class="town-info">
                            <?= Html::encode($model->city->name); ?>
                        </span>
                        <?php
                        if ( ! empty($model->userSettings->birthday)): ?>
                            <span class="age-info">
                                <?= date_diff(
                                        new DateTime('now'),
                                        new DateTime(
                                            $model->userSettings->birthday
                                        )
                                    )->y.' лет'; ?>
                            </span>
                        <?php
                        endif; ?>
                    </p>
                </div>
            </div>
            <h4 class="head-regular">Отзывы заказчиков</h4>
            <?php
            foreach ($comments as $comment): ?>
                <div class="response-card">
                    <img class="customer-photo"
                         src="<?= $comment->user->userSettings->avatar ?? '' ?>"
                         width="120" height="127" alt="Фото заказчиков">
                    <div class="feedback-wrapper">
                        <?php
                        if ($comment->comment): ?>
                            <p class="feedback">
                                <?= Html::encode($comment->comment); ?>
                            </p>
                        <?php
                        endif; ?>
                        <p class="task">Задание
                            <a href="<?= Url::toRoute(
                                ['tasks/view', 'id' => $comment->task->id]
                            ); ?>" class="link link--small">
                                «<?= Html::encode($comment->task->name); ?>»
                            </a> выполнено
                        </p>
                    </div>
                    <div class="feedback-wrapper">
                        <?= UIHelper::getStarsByRate($comment->mark); ?>
                        <p class="info-text">
                            <span class="current-time">
                                <?= Yii::$app->formatter->asRelativeTime(
                                    $comment->task->finished
                                ); ?>
                            </span>
                        </p>
                    </div>
                </div>
            <?php
            endforeach; ?>
        </div>
        <div class="right-column">
            <div class="right-card black">
                <h4 class="head-card">Статистика исполнителя</h4>
                <dl class="black-list">
                    <dt>Всего заказов</dt>
                    <dd>
                        <?= Html::encode(
                            $model->performer->rating->finishedTasks
                        ); ?> выполнено,
                        <?= Html::encode(
                            $model->performer->rating->failedTasks
                        ); ?> провалено
                    </dd>
                    <dt>Место в рейтинге</dt>
                    <dd>
                        <?= Performer::getRatingPosition($model->id); ?>
                    </dd>
                    <dt>Дата регистрации</dt>
                    <dd>
                        <?= Yii::$app->formatter->asDateTime(
                            $model->dateRegistration,
                            'php:j F, H:i'
                        ); ?>
                    </dd>
                    <dt>Статус</dt>
                    <dd>
                        <?= Html::encode($model->performer->status->name); ?>
                    </dd>
                </dl>
            </div>
            <div class="right-card white">
                <h4 class="head-card">Контакты</h4>
                <ul class="enumeration-list">
                    <?php
                    if ( ! empty($model->userSettings->phone)): ?>
                        <li class="enumeration-item">
                            <a href="tel:<?= $model->userSettings->phone; ?>"
                               class="link link--block link--phone">
                                <?= Html::encode(
                                    $model->userSettings->phone
                                ); ?>
                            </a>
                        </li>
                    <?php
                    endif; ?>
                    <?php
                    if ($model->email): ?>
                        <li class="enumeration-item">
                            <a href="mailto:<?= $model->email; ?>"
                               class="link link--block link--email">
                                <?= Html::encode($model->email); ?>
                            </a>
                        </li>
                    <?php
                    endif; ?>
                    <?php
                    if ( ! empty($model->userSettings->telegram)): ?>
                        <li class="enumeration-item">
                            <a href="https://telegram.me/<?= $model->userSettings->telegram; ?>"
                               class="link link--block link--tg"
                               target="_blank">
                                <?= Html::encode(
                                    $model->userSettings->telegram
                                ); ?>
                            </a>
                        </li>
                    <?php
                    endif; ?>
                </ul>
            </div>
        </div>
    <?php
    endif; ?>
</main>
