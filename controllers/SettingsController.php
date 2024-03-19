<?php

namespace app\controllers;

use app\models\City;
use app\models\PasswordChangeForm;
use app\models\User;
use app\models\UserSettings;
use Yii;

class SettingsController extends SecuredController
{
    public function actionIndex()
    {
        $settings = $this->findOrDie(Yii::$app->user->id, UserSettings::class);
        $user = $this->findOrDie(Yii::$app->user->id, User::class);
        $cities = Yii::$app->cache->get('cities');

        if (!$cities) {
            $cities = City::find()->orderBy('name')->all();
        }

        if (Yii::$app->request->isPost) {
            $settings->load(Yii::$app->request->post());
            $user->load(Yii::$app->request->post());

            if (!empty($settings->categories_id)) {
                $settings->categories_id = implode(',', $settings->categories_id);
            }

            $settings->save(false);
            $user->save();
        }

        if (!empty($settings->categories_id)) {
            $settings->categories_id = explode(',', $settings->categories_id);
        }

        return $this->render('index', ['settings' => $settings, 'user' => $user, 'cities' => $cities]);
    }

    public function actionSecurity()
    {
        $user = $this->findOrDie(Yii::$app->user->id, User::class);
        $settings = $this->findOrDie(Yii::$app->user->id, UserSettings::class);
        $passwordChangeForm = new PasswordChangeForm($user);
        $settings->scenario = 'update';

        if (Yii::$app->request->isPost) {
            $user->load(Yii::$app->request->post());
            $passwordChangeForm->load(Yii::$app->request->post());
            $settings->load(Yii::$app->request->post());

            if ($passwordChangeForm->validate() && !empty($passwordChangeForm->currentPassword)) {
                $passwordChangeForm->changePassword();
            }

            $settings->save(false);
            $user->save();
        }

        return $this->render(
            'security',
            ['user' => $user, 'passwordChangeForm' => $passwordChangeForm, 'settings' => $settings]
        );
    }
}
