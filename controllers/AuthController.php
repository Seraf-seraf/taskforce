<?php

namespace app\controllers;

use app\models\City;
use app\models\LoginForm;
use app\models\Performer;
use app\models\Rating;
use app\models\User;
use app\models\UserSettings;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class AuthController extends Controller
{

    public function actionSignup()
    {
        $model  = new User();
        $cities = City::find()->orderBy('name')->all();

        if (Yii::$app->request->getIsPost()) {
            $model->load(Yii::$app->request->post());

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($model);
            }

            if ($model->validate()) {
                $model->password = Yii::$app->security->generatePasswordHash(
                    $model->password
                );
                $model->save(false);

                $userSettings          = new UserSettings();
                $userSettings->user_id = $model->id;
                $userSettings->save(false);

                if ($model->isPerformer) {
                    $performer = new Performer();
                    $rating    = new Rating();

                    $performer->performer_id = $model->id;
                    $rating->performer_id    = $model->id;

                    $performer->save(false);
                    $rating->save(false);
                }
                $this->goHome();
            }
        }

        return $this->render(
            'signup',
            [
                'model'  => $model,
                'cities' => $cities,
            ]
        );
    }

    public function actionLogin()
    {
        $loginForm = new LoginForm();

        if (Yii::$app->request->isPost) {
            $loginForm->load(Yii::$app->request->post());

            if ($loginForm->validate()) {
                Yii::$app->user->login($loginForm->getUser());

                return $this->goHome();
            }
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        $this->goHome();
    }

}