<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ErrorController extends Controller
{
    public function actionNotfound()
    {
        $error = new NotFoundHttpException('Страница не найдена');
        return $this->render('error404', ['error' => $error]);
    }
}