<?php

namespace app\controllers;

use app\models\Auth;
use app\models\City;
use app\models\LoginForm;
use app\models\Performer;
use app\models\Rating;
use app\models\User;
use app\models\UserSettings;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class AuthController extends Controller
{

    public function actions(): array
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess($client): void
    {
        $attributes = $client->getUserAttributes();

        /* @var $auth Auth */
        $auth = Auth::find()->where([
            'source' => $client->getId(),
            'source_id' => $attributes['id'],
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) { // авторизация
                $user = $auth->user;
                Yii::$app->user->login($user);
            } else { // регистрация
                if (isset($attributes['email']) && User::find()->where(['email' => $attributes['email']])->exists()) {
                            echo "Пользователь с такой электронной почтой уже существует";
                    die();
                } else {
                    $password = Yii::$app->security->generateRandomString(24);
                    $city = City::findOne(['name' => $attributes['city']['title']]) ?? 0;

                    /* Форма регистрации отправляет ajax, пользователь устанавливает или снимает чекбокс
                    * в сессию записывается 0 или 1
                    * */
                    $isPerformer = Yii::$app->session->get('user_role') ?? 0;

                    $user = new User();
                    $user->name = $attributes['first_name'] . ' ' . $attributes['last_name'];
                    $user->email = $attributes['email'];
                    $user->city_id = $city->id;
                    $user->password = Yii::$app->security->generatePasswordHash($password);
                    $user->isPerformer = $isPerformer;

                    $userSettings = new UserSettings([
                        'avatar' => $attributes['photo'],
                        'birthday' => date('Y-m-d', strtotime($attributes['bdate']))
                    ]);

                    $transaction = $user->getDb()->beginTransaction();
                    if ($user->save()) {
                        $auth = new Auth([
                            'user_id' => $user->id,
                            'source' => $client->getId(),
                            'source_id' => (string)$attributes['id'],
                        ]);
                        $userSettings->link('user', $user);
                        $userSettings->save();

                        $rolesManager = Yii::$app->authManager;
                        if ($isPerformer) {
                            $performer = new Performer([
                                'performer_id' => $user->id
                            ]);
                            $performer->save();

                            $performerRating = new Rating([
                                'performer_id' => $user->id
                            ]);
                            $performerRating->save();

                            $performerRole = $rolesManager->getRole('performer');
                            $rolesManager->assign($performerRole, $user->id);
                        } else {
                            $clientRole = $rolesManager->getRole('client');
                            $rolesManager->assign($clientRole, $user->id);
                        }

                        if ($auth->save()) {
                            $transaction->commit();
                            Yii::$app->user->login($user);
                        } else {
                            print_r($auth->getErrors());
                        }
                    } else {
                        print_r($user->getErrors());
                    }
                }
            }
        } else { // Пользователь уже зарегистрирован
            if (!$auth) { // добавляем внешний сервис аутентификации
                $auth = new Auth([
                    'user_id' => Yii::$app->user->id,
                    'source' => $client->getId(),
                    'source_id' => $attributes['id'],
                ]);
                $auth->save();
            }
        }
    }

    public function actionSignup(): Response|array|string
    {
        $user = new User();
        $cities = City::find()->orderBy('name')->all();
        Yii::$app->cache->set('cities', $cities);

        if (Yii::$app->request->isAjax) {
            $user->load(Yii::$app->request->post());
            Yii::$app->session->set('user_role', $user->isPerformer);

            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($user);
        }

        if (Yii::$app->request->isPost) {
            $user->load(Yii::$app->request->post());

            if ($user->validate()) {
                $user->password = Yii::$app->security->generatePasswordHash($user->password);
                $user->save(false);

                $userSettings          = new UserSettings();
                $userSettings->user_id = $user->id;
                $userSettings->save();
                $rolesManager = Yii::$app->authManager;

                if ($user->isPerformer) {
                    $performer = new Performer();
                    $rating    = new Rating();

                    $performer->performer_id = $user->id;
                    $rating->performer_id = $user->id;


                    $performerRole = $rolesManager->getRole('performer');
                    $rolesManager->assign($performerRole, $user->id);

                    $performer->save();
                    $rating->save();
                } else {
                    $clientRole = $rolesManager->getRole('client');
                    $rolesManager->assign($clientRole, $user->id);
                }

                Yii::$app->user->login(User::findIdentity($user->id));

                return $this->goHome();
            }
        }

        return $this->render(
            'signup', [
                'user' => $user,
                'cities' => Yii::$app->cache->get('cities'),
            ]
        );
    }

    public function actionLogin()
    {
        $loginForm = new LoginForm();

        if (Yii::$app->request->isPost) {
            $loginForm->load(Yii::$app->request->post());

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($loginForm);
            }

            if ($loginForm->validate()) {
                Yii::$app->user->login($loginForm->getUser());

                return $this->goHome();
            }
        }
    }

    public function actionLogout(): void
    {
        Yii::$app->user->logout();
        $this->goHome();
    }

}