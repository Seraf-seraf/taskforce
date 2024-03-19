<?php
/** @var \app\controllers\TasksController $task */
/** @var \app\controllers\TasksController $performer */
/** @var \app\controllers\TasksController $performer_response */

use app\assets\AppAsset;
use app\assets\YandexMapAsset;
use app\helpers\UIHelper;
use TaskForce\logic\AvailableActions;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;

$this->title = 'Задание';
YandexMapAsset::register($this);
$this->registerJsFile('js/main.js');
AppAsset::register($this);
?>

<main class="main-content container">
    <div class="left-column">
        <div class="head-wrapper">
            <h3 class="head-main"><?= Html::encode($task->name); ?></h3>
            <?= Html::tag(
                'p',
                empty($model->budget) ? '' : Html::encode($model->budget) . ' ₽',
                ['class' => 'price price--big']
            ); ?>
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
        <?php
        if (!empty($task->location)): ?>
            <div class="task-map">
                <div class="map" id="map">
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            ymaps.ready(init);

                            function init() {
                                const myMap = new ymaps.Map("map", {
                                    center: [<?= "$task->lat, $task->long"; ?>],
                                    zoom: 16
                                });
                                myMap.controls.remove('rulerControl');
                                myMap.controls.remove('searchControl');
                                myMap.controls.remove('trafficControl');
                                myMap.controls.remove('typeSelector');
                                myMap.controls.remove('routeEditor');
                                myMap.controls.remove('fullscreenControl');
                                myMap.controls.remove('routeButtonControl');
                                myMap.controls.remove('routePanelControl');


                                var myPlacemark = new ymaps.Placemark(
                                    [<?= "$task->lat, $task->long"; ?>],
                                    {},
                                    {
                                        preset: 'image#default',
                                    });
                                myMap.geoObjects.add(myPlacemark);
                            }
                        });
                    </script>
                </div>
                <?= Html::tag('p', Html::encode($task->city->name), ['class' => 'map-address town']); ?>
                <?= Html::tag('p', Html::encode($task->location), ['class' => 'map-address']); ?>
            </div>
        <?php
        endif; ?>
        <?php
        if ($task->taskStatus_id != AvailableActions::STATUS_CANCEL && $task->taskStatus_id != AvailableActions::STATUS_EXPIRED): ?>
            <?php
            if (empty($performer)): ?>
                <h4 class="head-regular">Отклики на задание</h4>
                <?php
                foreach ($task->responses as $response): ?>
                    <div class="response-card">
                        <?= Html::img(
                                $response->performer->user->userSettings->avatar,
                            [
                                'class' => "customer-photo",
                                'width' => "146",
                                'height' => "156",
                                'alt' => "Фото исполнителя"
                            ]
                        ); ?>
                        <div class="feedback-wrapper">
                            <?= Html::a(
                                Html::encode($response->performer->user->name),
                                Url::toRoute(['user/view', 'id' => $response->performer->user->id]),
                                ['class' => 'link link--block link--big']
                            ); ?>
                            <div class="response-wrapper">
                                <?= UIHelper::getStarsByRate('small', $response->performer->rating->userRating); ?>
                                <p class="reviews">
                                    <?= $response->performer->getComments()->count()
                                    . ' '
                                    . UIHelper::pluralize(
                                        $response->performer->getComments()
                                            ->count(),
                                        ['отзыв', 'отзыва', 'отзывов']
                                    );
                                    ?>
                                </p>
                            </div>
                            <?= Html::tag('p', Html::encode($response->comment), ['class' => 'response-message']); ?>
                        </div>
                        <div class="feedback-wrapper">
                            <p class="info-text">
                                <span class="current-time">
                                    <?= Yii::$app->formatter->asRelativeTime($response->dateCreate); ?>
                                </span>
                            </p>
                            <?= Html::tag('p', Html::encode($response->price) . ' ₽', ['class' => 'price price--small']
                            ); ?>
                        </div>
                        <?php
                        if ($task->client_id == $user->id && !$response->isRejected): ?>
                            <div class="button-popup">
                                <?= Html::a(
                                    'Принять',
                                    Url::to(['responses/accept', 'id' => $response->id]),
                                    ['class' => 'button button--blue button--small']
                                ); ?>
                                <?= Html::a(
                                    'Отказать',
                                    Url::to(['responses/deny', 'id' => $response->id]),
                                    ['class' => 'button button--orange button--small']
                                ); ?>
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
                    <?= Html::img(
                            $performer->user->userSettings->avatar,
                        [
                            'class' => "customer-photo",
                            'width' => "146",
                            'height' => "156",
                            'alt' => "Фото исполнителя"
                        ]
                    ); ?>
                    <div class="feedback-wrapper">
                        <?= Html::a(
                            Html::encode($performer->user->name),
                            Url::toRoute(['user/view', 'id' => $performer->user->id]),
                            ['class' => 'link link--block link--big']
                        ); ?>
                        <div class="response-wrapper">
                            <?= UIHelper::getStarsByRate('small', $performer->rating->userRating); ?>
                            <?= Html::tag(
                                'p',
                                $performer->getComments()->count() . ' ' . UIHelper::pluralize(
                                    $performer->getComments()->count(),
                                    ['отзыв', 'отзыва', 'отзывов']
                                ),
                                ['class' => 'reviews']
                            ); ?>
                        </div>
                        <?= Html::tag('p', Html::encode($performer_response->comment), ['class' => 'response-message']
                        ); ?>
                    </div>
                </div>
            <?php
            endif; ?>
        <?php
        endif; ?>
    </div>
    <div class="right-column">
        <div class="right-card black info-card">
            <h4 class="head-card">Информация о задании</h4>
            <dl class="black-list">
                <dt>Категория</dt>
                <dd><?= Html::encode($task->category->name); ?></dd>
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
<?php
if (empty($performer)): ?>
    <section class="pop-up pop-up--action_response pop-up--close">
        <div class="pop-up--wrapper">
            <h4>Добавление отклика к заданию</h4>
            <p class="pop-up-text">
                Вы собираетесь оставить свой отклик к этому заданию.
                Пожалуйста, укажите стоимость работы и добавьте комментарий,
                если необходимо.
            </p>
            <div class="addition-form pop-up--form regular-form">
                <?php
                $form = ActiveForm::begin([
                    'enableAjaxValidation' => true,
                    'validationUrl' => ['responses/validate', 'id' => $task->id],
                    'action' => Url::to(['responses/create', 'id' => $task->id]),
                ]); ?>
                <?= $form->field($newResponse, 'comment', ['template' => '{label}{input}{error}'])->textarea(); ?>
                <div class="half-wrapper">
                    <?= $form->field($newResponse, 'price')->textInput(); ?>
                </div>
                <?= Html::submitInput('Завершить', ['class' => 'button button--pop-up button--blue']); ?>
                <?php
                ActiveForm::end(); ?>
            </div>
            <div class="button-container">
                <button class="button--close" type="button">Закрыть окно
                </button>
            </div>
        </div>
    </section>
<?php
else: ?>
    <section class="pop-up pop-up--action_deny pop-up--close">
        <div class="pop-up--wrapper">
            <h4>Отказ от задания</h4>
            <p class="pop-up-text">
                <b>Внимание!</b><br>
                Вы собираетесь отказаться от выполнения этого задания.<br>
                Это действие плохо скажется на вашем рейтинге и увеличит счетчик
                проваленных заданий.
            </p>
            <a href="<?= Url::toRoute(['tasks/deny', 'id' => $task->id]); ?>"
               class="button button--pop-up button--orange">Отказаться</a>
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
                <?php
                $form = ActiveForm::begin([
                    'enableAjaxValidation' => true,
                    'validationUrl' => ['comments/validate', 'id' => $task->id],
                    'action' => Url::to(['comments/create', 'id' => $task->id]),
                ]); ?>
                <?= $form->field($comment, 'comment')->textarea(); ?>
                <?= $form->field(
                    $comment,
                    'mark',
                    ['template' => '{label}{input}' . UIHelper::getStarsByRate('big', 0, true) . '{error}']
                )->hiddenInput(); ?>
                <?= Html::submitInput('Завершить', ['class' => 'button button--pop-up button--blue']); ?>
                <?php
                ActiveForm::end(); ?>
            </div>
            <div class="button-container">
                <button class="button--close" type="button">
                    Закрыть окно
                </button>
            </div>
        </div>
    </section>
<?php
endif; ?>
<div class="overlay"></div>
