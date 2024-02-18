<?php
namespace app\controllers;

use app\models\File;
use app\models\Task;
use app\models\TaskCategories;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class TasksController extends SecuredController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow'         => false,
                        'roles'         => ['@'],
                        'actions'       => ['create'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->identity->isPerformer) {
                                return true;
                            }
                        },
                        'denyCallback'  => function ($rule, $action) {
                            return $this->goHome();
                        },
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $task = new Task();

        if (Yii::$app->request->isPost) {
            $task->load(Yii::$app->request->post());
            Yii::$app->session['filters'][] = $task->attributes;
            Yii::$app->session['filters'][] = [
                'noLocation' => $task->noLocation,
                'noResponses' => $task->noResponses,
                'filterPeriod' => $task->filterPeriod
            ];
        } else {
            $task->attributes = Yii::$app->session->get('filters', []);
        }

        $tasksQuery = $task->getSearchQuery()->with('category');

        $pages = new Pagination([
            'totalCount' => $tasksQuery->count(),
            'pageSize' => 5,
            'forcePageParam' => false,
            'pageSizeParam' => false,
        ]);

        $models = $tasksQuery
          ->offset($pages->offset)
          ->limit($pages->limit)
          ->all();

        $categories = TaskCategories::find()->all();

        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
            'task' => $task,
            'categories' => $categories,
            'tasksQuery' => $tasksQuery
        ]);
    }

    public function actionView($id): string
    {
        $task = Task::find()
                     ->where(['id' => $id])
                     ->with([
                         'responses' => function ($query) {
                             $query->with(
                                 ['performer.userSettings', 'performer.rating']
                             );
                         },
                     ])->one();

        if ( ! $task) {
            $error = new NotFoundHttpException('Error 404', 404);

            return $this->render('view', ['error' => $error]);
        }

        return $this->render('view', ['task' => $task]);
    }

    public function actionCreate()
    {
        $task = new Task();
        $categories = TaskCategories::find()->all();

        if (!Yii::$app->session->has('task_uid')) {
            Yii::$app->session->set('task_uid', uniqid('upload'));
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

    public function actionUpload()
    {
        if (Yii::$app->request->isPost) {
            $model           = new File();
            $model->task_uid = Yii::$app->session->get('task_uid');
            $model->file     = UploadedFile::getInstanceByName('file');

            $model->upload();

            return $this->asJson($model->getAttributes());
        }
    }
}