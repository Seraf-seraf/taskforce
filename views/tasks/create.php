<?php
/** @var \app\controllers\TasksController $task */

/** @var \app\controllers\TasksController $categories */

use app\assets\DropzoneAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

DropzoneAsset::register($this);
$this->title = 'Создать задание';
?>
<main class="main-content main-content--center container">
    <div class="add-task-form regular-form">
        <?php $form = ActiveForm::begin([
            'action' => Url::toRoute(['tasks/create']),
            'options' => ['enctype' => 'multipart/form-data']
        ]); ?>
            <h3 class="head-main head-main">Публикация нового задания</h3>
                <?= $form->field($task, 'name')->textInput(['id' => 'essence-work']); ?>
                <?= $form->field($task, 'description')->textarea(); ?>
                <?= $form->field($task, 'category_id')->dropDownList(array_column($categories, 'name', 'id'),
                    ['prompt' => 'Выбрать категорию']); ?>
                <?= $form->field($task, 'location')->textInput(['class' => 'location-icon']); ?>
            <div class="half-wrapper">
                    <?= $form->field($task, 'budget')->textInput(['class' => 'budget-icon']); ?>
                    <?= $form->field($task, 'deadline')->input('date'); ?>
            </div>
            <p class="form-label">Файлы</p>
            <div class="new-file">
                <div class="dz-message">Добавьте или перетащите файлы</div>
                <div class="files-previews">

                </div>
            </div>

            <?= Html::submitInput('Опубликовать', ['class' => 'button button--blue']); ?>
        <?php ActiveForm::end(); ?>
    </div>
</main>
<?php
$url = Url::toRoute(['tasks/upload']);
$this->registerJs(<<<JS
const dropzone = new Dropzone(".new-file", {
    maxFiles: 4, 
    url: "$url", 
    previewsContainer: ".files-previews",
    sending: function (none, xhr, formData) {
        formData.append('_csrf', $('input[name=_csrf]').val());
    },
    dictMaxFilesExceeded: "Достигнут лимит загрузки файлов: {{maxFiles}}",
    autoProcessQueue: true,
    paramName: 'uploadedFile'
});
JS, View::POS_READY);
?>
