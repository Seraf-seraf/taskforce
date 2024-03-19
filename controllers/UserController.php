<?php

namespace app\controllers;

use app\models\Comments;
use app\models\Performer;
use app\models\Response;
use app\models\Task;
use app\models\TaskCategories;
use app\models\User;
use app\models\UserSettings;
use TaskForce\logic\AvailableActions;
use Yii;

class UserController extends SecuredController
{

    public function actionView(int $id): \yii\web\Response|string
    {
        $user = $this->findOrDie($id, User::class);
        $settings = $this->findOrDie($id, UserSettings::class);

        if (!$user || !$user->isPerformer) {
            return $this->redirect('error/notfound');
        }

        $responses = Response::find()
                             ->where(['performer_id' => $id])
                             ->all();

        $selectedCategoriesId = explode(
            ',',
            UserSettings::find()->where(['user_id' => $id])->select('categories_id')->scalar()
        );

        $categories = TaskCategories::findAll($selectedCategoriesId);

        $comments = Comments::find()
            ->innerJoin('task', 'task.id = comments.task_id')
            ->where(['comments.performer_id' => $id])
            ->orderBy('task.finished DESC')
            ->limit(5)
            ->all();

        if ($settings->privateContacts && Yii::$app->user->id != $id) {
            $showContacts = Task::find()->innerJoin('performer', 'performer.performer_id = task.performer_id')
                ->where(
                    ['taskStatus_id' => AvailableActions::STATUS_IN_PROGRESS, 'task.client_id' => Yii::$app->user->id]
                )
                ->count('name');
        }

        $ratingPosition = Performer::getRatingPosition($id);

        return $this->render('view', [
            'user'      => $user,
            'responses'  => $responses,
            'categories' => $categories,
            'comments'   => $comments,
            'ratingPosition' => $ratingPosition,
            'showContacts' => $showContacts ?? true
        ]);
    }

}
