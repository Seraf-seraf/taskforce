<?php
/** @var \app\controllers\UserController $responses */

/** @var \app\controllers\UserController $user */
/** @var \app\controllers\UserController $categories */
/** @var \app\controllers\UserController $error */
/** @var \app\controllers\UserController $comments */

use app\helpers\UIHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Профиль пользователя';
?>

<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main">
            <?= Html::encode($user->name); ?>
        </h3>
        <div class="user-card">
            <div class="photo-rate">
                <?= Html::img(
                        $user->userSettings->avatar,
                    [
                        'class' => "customer-photo",
                        'width' => "190",
                        'height' => "190",
                        'alt' => "Фото исполнителя"
                    ]
                ); ?>
                <div class="card-rate">
                    <?= UIHelper::getStarsByRate(
                        'big',
                        $user->rating->userRating
                    ) ?>
                    <span class="current-rate">
                    <?= Html::encode($user->rating->userRating); ?>
                </span>
                </div>
            </div>
            <?php
            if (!empty($user->userSettings->description)): ?>
                <?= Html::tag('p', Html::encode($user->userSettings->description), ['class' => 'user-description']); ?>
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
                            <?= Html::a(
                                Html::encode($category->name),
                                Url::toRoute(['tasks/index', 'category_id' => $category->id]),
                                ['class' => 'link link--regular']
                            ); ?>
                        </li>
                    <?php
                    endforeach; ?>
                </ul>
            </div>
            <div class="bio">
                <p class="head-info">Био</p>
                <p class="bio-info">
                    <span class="town-info">
                        <?= Html::encode($user->city->name); ?>
                    </span>
                    <?php
                    if (!empty($user->userSettings->birthday)): ?>
                        <span class="age-info">
                            <?= $user->userSettings->getAge() . ' лет'; ?>
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
                <?= Html::img(
                        $comment->client->userSettings->avatar,
                    [
                        'class' => "customer-photo",
                        'width' => "120",
                        'height' => "127",
                        'alt' => "Фото заказчика"
                    ]
                ); ?>
                <div class="feedback-wrapper">
                    <?= Html::tag('p', Html::encode($comment->comment) ?? '', ['class' => 'feedback']); ?>
                    <?= Html::tag(
                        'p',
                        'Задание «' . Html::a(
                            Html::encode($comment->task->name) . '»',
                            Url::toRoute(['tasks/view', 'id' => $comment->task->id]),
                            ['class' => 'link link--small']
                        ),
                        ['class' => 'task']
                    ); ?>
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
                        $user->performer->rating->finishedTasks
                    ); ?> выполнено,
                    <?= Html::encode(
                        $user->performer->rating->failedTasks
                    ); ?> провалено
                </dd>
                <dt>Место в рейтинге</dt>
                <dd>
                    <?= $ratingPosition; ?>
                </dd>
                <dt>Дата регистрации</dt>
                <dd>
                    <?= Yii::$app->formatter->asDateTime(
                        $user->dateRegistration,
                        'php:j F, H:i'
                    ); ?>
                </dd>
                <dt>Статус</dt>
                <dd>
                    <?= Html::encode($user->performer->status->name); ?>
                </dd>
            </dl>
        </div>
        <?php
        if ($showContacts): ?>
            <div class="right-card white">
                <h4 class="head-card">Контакты</h4>
                <ul class="enumeration-list">
                    <?php
                    if ( ! empty($user->userSettings->phone)): ?>
                        <li class="enumeration-item">
                            <?= Html::a(
                                Html::encode($user->userSettings->phone),
                                'tel:' . Html::encode($user->userSettings->phone),
                                ['class' => 'link link--block link--phone']
                            ); ?>
                        </li>
                    <?php
                    endif; ?>
                        <li class="enumeration-item">
                            <?= Html::mailto(
                                Html::encode($user->email),
                                Html::encode($user->email),
                                ['class' => 'link link--block link--email']
                            ); ?>
                        </li>
                    <?php
                    if ( ! empty($user->userSettings->telegram)): ?>
                        <li class="enumeration-item">
                            <?= Html::a(
                                Html::encode($user->userSettings->telegram),
                                Url::to(
                                    'https://telegram.me/' . mb_substr(Html::encode($user->userSettings->telegram), 1)
                                ),
                                ['class' => 'link link--block link--phone', 'target' => '_blank']
                            ); ?>
                        </li>
                    <?php
                    endif; ?>
                </ul>
            </div>
        <?php
        endif; ?>
    </div>
</main>
