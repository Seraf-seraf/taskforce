<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

abstract class SecuredController extends Controller
{

    public function behaviors(): array
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

    public function getUser(): bool|\yii\web\IdentityInterface|null
    {
        return Yii::$app->user->getIdentity();
    }

    public function isPerformer(): int
    {
        return User::findOne(Yii::$app->user->id)->isPerformer;
    }

    public function findOrDie($id, $class)
    {
        $model = $class::findOne($id);

        if (!$model) {
            return $this->redirect(['error/notfound']);
        } else {
            return $model;
        }
    }
}