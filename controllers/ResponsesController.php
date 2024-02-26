<?php

namespace app\controllers;

use app\models\Performer;
use app\models\Response;
use TaskForce\logic\AvailableActions;
use Yii;

class ResponsesController extends SecuredController
{

    public const RESPONSE_REJECTED = 1;

    public const RESPONSE_ACCEPTED = 1;


    public function actionDeny($id)
    {
        $response = Response::findOne($id);

        $this->isPerformer(Yii::$app->user->id);

        $response->isRejected = self::RESPONSE_REJECTED;

        $response->save();

        return $this->redirect(['tasks/view', 'id' => $response->task_id]);
    }

    public function actionAccept($id)
    {
        $response = Response::findOne($id);

        $this->isPerformer(Yii::$app->user->id);

        $response->task->taskStatus_id = AvailableActions::STATUS_IN_PROGRESS;

        $performer = Performer::findOne($response->performer_id);
        $performer->status_id = AvailableActions::PERFORMER_BUSY;
        $performer->task_id = $response->task_id;

        $response->task->save(false);
        $response->save();
        $performer->save();

        return $this->redirect(['tasks/view', 'id' => $response->task_id]);
    }

}