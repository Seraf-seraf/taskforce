<?php
namespace app\controllers;

use app\models\File;
use app\models\Performer;
use app\models\Task;
use app\models\TaskCategories;
use TaskForce\logic\AvailableActions;
use Yii;
use yii\data\Pagination;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

class TasksController extends SecuredController
{

    public function actionIndex(): string
    {
        $task = new Task();

        if (Yii::$app->request->isPost) {
            $task->load(Yii::$app->request->post());
            $filters = Yii::$app->session->get('filters');

            $filters[] = $task->attributes;
            $filters[] = [
                'noLocation' => $task->noLocation,
                'noResponses' => $task->noResponses,
                'filterPeriod' => $task->filterPeriod,
            ];
        } else {
            $task->attributes = Yii::$app->session->get('filters');
        }

        $tasksQuery = $task->getSearchQuery()->with('category');

        $pages = new Pagination([
            'totalCount' => $tasksQuery->count(),
            'pageSize' => 5,
            'forcePageParam' => false,
            'pageSizeParam' => false,
        ]);

        $models = $tasksQuery->offset($pages->offset)->limit($pages->limit)->all();

        $categories = TaskCategories::find()->all();

        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
            'task' => $task,
            'categories' => $categories,
            'tasksQuery' => $tasksQuery,
        ]);
    }

    public function actionView($id)
    {
        $task = Task::find()
                    ->where(['id' => $id])
                    ->with([
                        'responses' => function ($query) {
                            $query->andWhere(['isRejected' => 0])
                                  ->orderBy(['dateCreate' => SORT_ASC]);
                        },
                    ])
                    ->one();

        if (!$task) {
            return $this->redirect('error');
        }

        if ($task->taskStatus_id != AvailableActions::STATUS_NEW) {
            $performer = Performer::find()->where(['task_id' => $task->id])->with('user')->one();
            $performer_response = \app\models\Response::find()->where(['performer_id' => $performer->performer_id, 'task_id' => $task->id])->one();

            return $this->render('view', ['task' => $task, 'performer' => $performer, 'performer_response' => $performer_response]);
        }

        return $this->render('view', ['task' => $task]);
    }

    public function actionCreate()
    {
        if (Yii::$app->user->identity->isPerformer) {
            return $this->goHome();
        }

        $task = new Task();
        $categories = TaskCategories::find()->all();

        if (!Yii::$app->session->has('task_uid')) {
            Yii::$app->session->set('task_uid', uniqid('upload'));
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($task);
        }

        if (Yii::$app->request->isPost) {
            $task->load(Yii::$app->request->post());
            $task->uid = Yii::$app->session->get('task_uid');
            $task->save();

            if ($task->id) {
                Yii::$app->session->remove('task_uid');
                return $this->redirect(['tasks/view', 'id' => $task->id]);
            }
        }

        return $this->render('create', ['task' => $task, 'categories' => $categories]);
    }

    public function actionUpload(): ?Response
    {
        if (Yii::$app->request->isPost) {
            $model           = new File();
            $model->task_uid = Yii::$app->session->get('task_uid');
            $model->uploadedFile = UploadedFile::getInstanceByName('uploadedFile');
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
            $model->upload();
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();
            return $this->asJson($model->getAttributes());
        }
        return null;
    }

    public function actionResponse()
    {

    }
}