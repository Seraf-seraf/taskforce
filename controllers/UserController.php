<?php

namespace app\controllers;

use app\models\Comments;
use app\models\Performer;
use app\models\Response;
use app\models\User;
use yii\web\NotFoundHttpException;

class UserController extends SecuredController
{

    public function actionView($id): string
    {
        $performer_id = Performer::find()
                                 ->where(['performer_id' => $id])
                                 ->one();

        if (empty($performer_id)) {
            $error = new NotFoundHttpException('Error 404', 404);

            return $this->render('view', ['error' => $error]);
        }

        $model = User::find()
                     ->with(['userSettings', 'performer.status'])
                     ->where(['id' => $id])
                     ->one();

        $responses = Response::find()
                             ->with('task.category')
                             ->where(['performer_id' => $id])
                             ->all();

        $categories = array_unique(
            array_map(function ($response) {
                return $response->task->category->name;
            }, $responses)
        );

        $comments = Comments::find()
                            ->with('author.tasks')
                            ->where(['performer_id' => $id])
                            ->all();

        return $this->render('view', [
            'model'      => $model,
            'responses'  => $responses,
            'categories' => $categories,
            'comments'   => $comments,
        ]);
    }

}
