<?php
/** @var \Exception $error */
use yii\helpers\Url;
use yii\helpers\Html;
?>

<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main"><?= $error->getMessage(); ?></h3>
        <?= Html::a('Вернуться обратно', Url::to('about'), ['class' => 'link'])?>
    </div>
</main>
