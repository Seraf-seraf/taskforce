<?php

namespace app\controllers;

use app\models\LoginForm;
use yii\filters\AccessControl;
use yii\web\Controller;

class LandingController extends Controller
{

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow'        => false,
                        'roles'        => ['@'],
                        'denyCallback' => function ($rule, $action) {
                            return $this->goHome();
                        },
                    ],
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $this->layout = '/landing';

        return $this->render('login', ['model' => new LoginForm()]);
    }

}