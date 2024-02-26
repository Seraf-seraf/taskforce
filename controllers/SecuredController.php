<?php

namespace app\controllers;

use app\models\User;
use yii\filters\AccessControl;
use yii\web\Controller;

abstract class SecuredController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }


    public function getUser()
    {
        return \Yii::$app->user->getIdentity();
    }

    public function isPerformer($id)
    {
        if (User::findOne($id)->isPerformer) {
            return $this->goHome();
        }
    }
}