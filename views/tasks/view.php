<?php
/** @var \app\controllers\TasksController $task */

/** @var \app\controllers\TasksController $performer */
/** @var \app\controllers\TasksController $performer_response */

use app\helpers\UIHelper;
use TaskForce\logic\AvailableActions;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Задание';
$user = Yii::$app->user->identity;
$this->registerJsFile('js/main.js');
?>

<main class="main-content container">
    <div class="left-column">
        <div class="head-wrapper">
            <h3 class="head-main"><?= Html::encode($task->name); ?></h3>
            <p class="price price--big"><?= empty($task->budget) ? 'Договорная' : Html::encode($task->budget).' ₽'; ?></p>
        </div>
        <p class="task-description"><?= empty($task->description) ? 'Нет описания' : Html::encode($task->description); ?></p>
        <?php
        if ($task->taskStatus_id == AvailableActions::STATUS_NEW || $task->taskStatus_id == AvailableActions::STATUS_IN_PROGRESS): ?>
            <?php
            foreach (
                UIHelper::getActionButtons($task, $user,
                    $task->performer) as $button
            ): ?>
                <?= $button; ?>
            <?php
            endforeach; ?>
        <?php
        endif; ?>
        <div class="task-map">
            <img class="map" src="img/map.png" width="725" height="346"
                 alt="Новый арбат, 23, к. 1">
            <p class="map-address town">Москва</p>
            <p class="map-address">Новый арбат, 23, к. 1</p>
        </div>
        <?php
        if (empty($performer)): ?>
            <h4 class="head-regular">Отклики на задание</h4>
            <?php
            foreach ($task->responses as $response): ?>
                <div class="response-card">
                    <img class="customer-photo" src="<?= Html::encode(
                        $response->performer->userSettings->avatar ?? ''
                    ); ?>" width="146" height="156" alt="Фото заказчиков">
                    <div class="feedback-wrapper">
                        <a href="<?= Url::toRoute([
                                'user/view',
                                'id' => $response->performer->id,
                            ]
                        ); ?>" class="link link--block link--big">
                            <?= Html::encode($response->performer->name); ?>
                        </a>
                        <div class="response-wrapper">
                            <?= UIHelper::getStarsByRate($response->performer->rating->userRating); ?>
                            <p class="reviews">
                                <?= $response->performer->getComments()->count()
                                    .' '
                                    .UIHelper::pluralize(
                                    $response->performer->getComments()
                                                        ->count(),
                                    ['отзыв', 'отзыва', 'отзывов']
                                );
                                ?>
                            </p>
                        </div>
                        <p class="response-message">
                            <?= Html::encode($response->comment); ?>
                        </p>
                    </div>
                    <div class="feedback-wrapper">
                        <p class="info-text">
                            <span class="current-time">
                                <?= Yii::$app->formatter->asRelativeTime($response->dateCreate); ?>
                            </span>
                        </p>
                        <p class="price price--small"><?= $response->price.' ₽'; ?></p>
                    </div>
                    <?php
                    if ($task->client_id == $user->id && ! $response->isRejected): ?>
                        <div class="button-popup">
                            <a href="<?= Url::toRoute([
                                'responses/accept', 'id' => $response->id,
                            ]) ?>" class="button button--blue button--small">Принять</a>
                            <a href="<?= Url::toRoute([
                                'responses/deny', 'id' => $response->id,
                            ]) ?>" class="button button--orange button--small">Отказать</a>
                        </div>
                    <?php
                    endif; ?>
                </div>
            <?php
            endforeach; ?>
        <?php
        else: ?>
            <h4 class="head-regular">Исполнитель</h4>
            <div class="response-card">
                <img class="customer-photo" src="<?= Html::encode(
                    $performer->user->userSettings->avatar ?? ''
                ); ?>" width="146" height="156" alt="Фото заказчиков">
                <div class="feedback-wrapper">
                    <a href="<?= Url::toRoute([
                            'user/view',
                            'id' => $performer->performer_id,
                        ]
                    ); ?>" class="link link--block link--big">
                        <?= Html::encode($performer->user->name); ?>
                    </a>
                    <div class="response-wrapper">
                        <?= UIHelper::getStarsByRate($performer->rating->userRating); ?>
                        <p class="reviews">
                            <?= $performer->user->getComments()->count()
                                .' '
                                .UIHelper::pluralize(
                                $performer->user->getComments()->count(),
                                ['отзыв', 'отзыва', 'отзывов']
                            );
                            ?>
                        </p>
                    </div>
                    <p class="response-message">
                        <?= Html::encode($performer_response->comment); ?>
                    </p>
                </div>
                <div class="feedback-wrapper">
                    <p class="info-text">
                            <span class="current-time">
                                <?= Yii::$app->formatter->asRelativeTime($performer_response->dateCreate); ?>
                            </span>
                    </p>
                    <p class="price price--small"><?= $performer_response->price.' ₽'; ?></p>
                </div>
            </div>
        <?php
        endif; ?>
    </div>
    <div class="right-column">
        <div class="right-card black info-card">
            <h4 class="head-card">Информация о задании</h4>
            <dl class="black-list">
                <dt>Категория</dt>
                <dd><?= $task->category->name; ?></dd>
                <dt>Дата публикации</dt>
                <dd>
                    <?= Yii::$app->formatter->asRelativeTime($task->dateCreate); ?>
                </dd>
                <dt>Срок выполнения</dt>
                <dd>
                    <?= Yii::$app->formatter->asDateTime($task->deadline,
                        'php:j F, H:i'); ?>
                </dd>
                <dt>Статус</dt>
                <dd>
                    <?= Html::encode($task->taskStatus->name); ?>
                </dd>
            </dl>
        </div>
        <?php
        if ( ! empty($task->files)): ?>
            <div class="right-card white file-card">
                <h4 class="head-card">Файлы задания</h4>
                <?= ListView::widget([
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $task->files,
                    ]),
                    'itemView'     => function ($file) {
                        $content = [
                            Html::a($file->name, Html::encode($file->path),
                                ['class' => 'link link--block link--clip']),
                            Html::tag('p',
                                Yii::$app->formatter->asShortSize($file->size),
                                ['class' => 'file-size']),
                        ];

                        return Html::tag('li', implode('', $content),
                            ['class' => 'enumeration-item']);
                    },
                    'itemOptions'  => [
                        'tag' => 'ul', 'class' => 'enumeration-list',
                    ],
                    'summary'      => false,
                ]); ?>
            </div>
        <?php
        endif; ?>
    </div>
</main>
<section class="pop-up pop-up--action_deny pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Отказ от задания</h4>
        <p class="pop-up-text">
            <b>Внимание!</b><br>
            Вы собираетесь отказаться от выполнения этого задания.<br>
            Это действие плохо скажется на вашем рейтинге и увеличит счетчик
            проваленных заданий.
        </p>
        <a class="button button--pop-up button--orange">Отказаться</a>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно
            </button>
        </div>
    </div>
</section>
<section class="pop-up pop-up--action_complete pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Завершение задания</h4>
        <p class="pop-up-text">
            Вы собираетесь отметить это задание как выполненное.
            Пожалуйста, оставьте отзыв об исполнителе и отметьте отдельно,
            если возникли проблемы.
        </p>
        <div class="completion-form pop-up--form regular-form">
            <form>
                <div class="form-group">
                    <label class="control-label" for="completion-comment">Ваш
                        комментарий</label>
                    <textarea id="completion-comment"></textarea>
                </div>
                <p class="completion-head control-label">Оценка работы</p>
                <div class="stars-rating big active-stars">
                    <span type="radio" id="star" data-star="1"></span>
                    <span type="radio" id="star" data-star="2"></span>
                    <span type="radio" id="star" data-star="3"></span>
                    <span type="radio" id="star" data-star="4"></span>
                    <span type="radio" id="star" data-star="5"></span>
                </div>
                <input type="submit"
                       class="button button--pop-up button--blue"
                       value="Завершить">
            </form>
        </div>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно
            </button>
        </div>
    </div>
</section>
<section class="pop-up pop-up--action_response pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Добавление отклика к заданию</h4>
        <p class="pop-up-text">
            Вы собираетесь оставить свой отклик к этому заданию.
            Пожалуйста, укажите стоимость работы и добавьте комментарий,
            если необходимо.
        </p>
        <div class="addition-form pop-up--form regular-form">
            <form>
                <div class="form-group">
                    <label class="control-label" for="addition-comment">Ваш
                        комментарий</label>
                    <textarea id="addition-comment"></textarea>
                </div>
                <div class="form-group">
                    <label class="control-label"
                           for="addition-price">Стоимость</label>
                    <input id="addition-price" type="text">
                </div>
                <input type="submit"
                       class="button button--pop-up button--blue"
                       value="Завершить">
            </form>
        </div>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно
            </button>
        </div>
    </div>
</section>
<div class="overlay"></div>
