<?php

namespace app\controllers;

use app\models\Comments;
use app\models\Performer;
use app\models\PerformerStatus;
use app\models\Rating;
use app\models\Task;
use TaskForce\logic\actions\CompleteAction;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\widgets\ActiveForm;

class CommentsController extends SecuredController
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['performer'],
                        'actions' => ['create', 'validate'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ]
            ]
        ];
    }

    public function actionCreate($id): Response
    {
        $task = $this->findOrDie($id, Task::class);
        $rating = $this->findOrDie($task->performer_id, Rating::class);
        $performer = $this->findOrDie($task->performer_id, Performer::class);
        $comment = new Comments();

        if (Yii::$app->request->isPost) {
            $comment->load(Yii::$app->request->post());

            $comment->client_id = $task->client_id;
            $comment->performer_id = $task->performer_id;
            $comment->task_id = $id;

            $performer->status_id = PerformerStatus::PERFORMER_FREE;

            $comment->save();
            $performer->save();

            $task->setFinishDate();
            $task->goToNextStatus(new CompleteAction());

            $rating->increaseFinishedTasks();
            $rating->getPerformerRating();
        }

        return $this->redirect(['tasks/view', 'id' => $id]);
    }

    public function actionValidate($id): array
    {
        $comment = new Comments();
        $task = $this->findOrDie($id, Task::class);

        $comment->performer_id = $task->performer_id;
        $comment->task_id = $id;

        if (Yii::$app->request->isAjax && $comment->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($comment);
        }
    }
}
