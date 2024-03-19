<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $client = $auth->createRole('client');
        $auth->add($client);

        $performer = $auth->createRole('performer');
        $auth->add($performer);
    }
}