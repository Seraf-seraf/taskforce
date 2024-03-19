<?php
/** @var app\controllers\TasksController $models */
/** @var app\controllers\TasksController $pages */
/** @var app\controllers\TasksController $categories */

use yii\helpers\BaseStringHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Новые задания';
?>
<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main head-task">Новые задания</h3>
        <?php foreach ($models as $model): ?>
            <div class="task-card">
                <div class="header-task">
                    <?= Html::a(
                        Html::encode($model->name),
                        Url::toRoute(['tasks/view', 'id' => $model->id]),
                        ['class' => "link link--block link--big"]
                    ); ?>
                    <?= Html::tag(
                        'p',
                        empty($model->budget) ? '' : Html::encode($model->budget) . ' ₽',
                        ['class' => 'price price--task']
                    ); ?>
                </div>
                <p class="info-text">
                    <span class="current-time">
                        <?= Yii::$app->formatter->asRelativeTime($model->dateCreate)?>
                    </span>
                </p>
                <?= Html::tag(
                    'p',
                    Html::encode(BaseStringHelper::truncate($model->description, 200)) ?? '',
                    ['class' => 'task-text']
                ); ?>
                <div class="footer-task">
                    <?php if($model->location): ?>
                        <?= Html::tag('p', Html::encode($model->city->name), ['class' => 'info-text town-text']); ?>
                    <?php endif ?>
                    <?= Html::tag(
                        'p',
                        Html::encode($model->category->name),
                        ['class' => 'info-text ' . Html::encode($model->category->icon) . ' category-text']
                    ); ?>
                    <?= Html::a(
                        'Смотреть задание',
                        Url::toRoute(['tasks/view', 'id' => $model->id]),
                        ['class' => 'button button--black']
                    ); ?>
                </div>
            </div>
        <?php endforeach ?>
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
    <div class="right-column">
       <div class="right-card black">
           <div class="search-form">
               <?php
               $form = ActiveForm::begin(); ?>
                    <h4 class="head-card">Категории</h4>
                    <div class="checkbox-wrapper">
                        <?= Html::activeCheckboxList($task, 'category_id', array_column($categories, 'name', 'id'),
                            ['tag' => null, 'itemOptions' => ['labelOptions' => ['class' => 'control-label']]]); ?>
                    </div>
                    <h4 class="head-card">Дополнительно</h4>
                    <div class="checkbox-wrapper">
                        <?= $form->field($task, 'noResponses')->checkbox(['labelOptions' => ['class' => 'control-label']]); ?>
                        <?= $form->field($task, 'noLocation')->checkbox(['labelOptions' => ['class' => 'control-label']]); ?>
                    </div>
                    <div class="checkbox-wrapper">
                        <h4 class="head-card">Период</h4>
                        <?=$form->field($task, 'filterPeriod', ['template' => '{input}'])->dropDownList([
                            '3600' => 'За последний час', '86400' => 'За сутки', '604800' => 'За неделю'
                        ], ['prompt' => 'Выбрать']); ?>
                    </div>
                    <?= Html::submitInput('Искать', ['class' => 'button button--blue']); ?>
                <?php ActiveForm::end(); ?>
            </div>
       </div>
    </div>
</main>