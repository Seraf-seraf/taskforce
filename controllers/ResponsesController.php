<?php

namespace app\controllers;

use app\models\PerformerStatus;
use app\models\Response;
use app\models\Task;
use TaskForce\logic\AvailableActions;
use Yii;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;

class ResponsesController extends SecuredController
{

    public const RESPONSE_REJECTED = 1;

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['perforemr'],
                        'actions' => ['deny', 'accept'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['client'],
                        'actions' => ['create'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionDeny(int $id): \yii\web\Response
    {

        $response = Response::findOne($id);

        $response->isRejected = self::RESPONSE_REJECTED;

        $response->save();

        return $this->redirect(['tasks/view', 'id' => $response->task_id]);
    }

    public function actionAccept(int $id): \yii\web\Response
    {
        if ($this->isPerformer()) {
            return $this->goHome();
        }

        $response = $this->findOrDie($id, Response::class);
        $task = $response->task;
        $performer = $response->performer;

        $task->taskStatus_id = AvailableActions::STATUS_IN_PROGRESS;

        $performer->status_id = PerformerStatus::PERFORMER_BUSY;

        $performer->link('task', $task);

        $task->save(false);
        $performer->save();

        return $this->redirect(['tasks/view', 'id' => $task->id]);
    }

    public function actionCreate(int $id): \yii\web\Response
    {
        $task = $this->findOrDie($id, Task::class);
        $newResponse = new Response();

        if (Yii::$app->request->isPost) {
            $newResponse->load(Yii::$app->request->post());

            $newResponse->task_id = $id;
            $newResponse->performer_id = Yii::$app->user->id;

            $newResponse->save(false);

            return $this->redirect(['tasks/view', 'id' => $task->id]);
        }
    }

    public function actionValidate(int $id): array
    {
        $newResponse = new Response();

        $newResponse->task_id = $id;
        $newResponse->performer_id = Yii::$app->user->id;

        if (Yii::$app->request->isAjax && $newResponse->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ActiveForm::validate($newResponse);
        }
    }
}