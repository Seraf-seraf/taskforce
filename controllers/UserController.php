<?php

namespace app\controllers;

use app\models\Comments;
use app\models\Response;
use app\models\User;

class UserController extends SecuredController
{

    public function actionView($id)
    {
        $user = User::find()
                     ->with(['userSettings', 'performer.status'])
                     ->where(['id' => $id])
                     ->one();

        if (!$user || !$user->isPerformer) {
            return $this->redirect('error');
        }

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
            'user'      => $user,
            'responses'  => $responses,
            'categories' => $categories,
            'comments'   => $comments,
        ]);
    }

}
