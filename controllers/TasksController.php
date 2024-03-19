<?php
namespace app\controllers;

use app\models\Comments;
use app\models\File;
use app\models\Performer;
use app\models\PerformerStatus;
use app\models\Rating;
use app\models\Task;
use app\models\TaskCategories;
use TaskForce\logic\actions\CancelAction;
use TaskForce\logic\actions\DenyAction;
use TaskForce\logic\AvailableActions;
use Yii;
use yii\data\Pagination;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\UploadedFile;

class TasksController extends SecuredController
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
                        'actions' => ['create', 'cancel', 'upload'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): Response|string
    {
        $task = new Task();

        $task->load(Yii::$app->request->post());

        $tasksQuery = $task->getSearchQuery()->with('category');

        $pages = new Pagination([
            'totalCount' => $tasksQuery->count(),
            'pageSize' => 5,
            'forcePageParam' => false,
            'pageSizeParam' => false,
        ]);

        $models = $tasksQuery->offset($pages->offset)->limit($pages->limit)->all();

        if (Yii::$app->request->isGet && !empty(Yii::$app->request->get('category_id'))) {
            if (in_array(Yii::$app->request->get('category_id'), array_column(TaskCategories::find()->all(), 'id'))) {
                $task->category_id = Yii::$app->request->get('category_id');
            } else {
                return $this->redirect('error/notfound');
            }
        }

        $categories = TaskCategories::find()->all();

        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
            'task' => $task,
            'categories' => $categories,
            'tasksQuery' => $tasksQuery,
        ]);
    }

    public function actionView(int $id): Response|string
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
            return $this->redirect('error/notfound');
        }

        if ($task->taskStatus_id != AvailableActions::STATUS_NEW && $task->taskStatus_id != AvailableActions::STATUS_CANCEL) {
            $performer = Performer::find()->where(['performer_id' => $task->performer_id])->with('user')->one();
            $performer_response = \app\models\Response::find()->where(['performer_id' => $performer->performer_id, 'task_id' => $task->id])->one();
            $comment = new Comments();

            return $this->render(
                'view',
                [
                    'task' => $task,
                    'performer' => $performer,
                    'performer_response' => $performer_response,
                    'comment' => $comment,
                    'user' => Yii::$app->user->identity
                ]
            );
        }

        $newResponse = new \app\models\Response();

        return $this->render(
            'view',
            ['task' => $task, 'newResponse' => $newResponse, 'user' => Yii::$app->user->identity]
        );
    }

    public function actionCreate(): Response|string
    {
        $task = new Task();
        $categories = TaskCategories::find()->all();

        Yii::$app->session->set('task_uid', uniqid('upload'));

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

    /**
     * Загрузка и привязка файлов к еще несозданной в бд задаче
     *
     * @return Response|null
     * @throws \yii\db\Exception
     */
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
        return $this->goHome();
    }

    public function actionCancel(int $id): Response
    {
        $task = $this->findOrDie($id, Task::class);
        $task->goToNextStatus(new CancelAction());
        return $this->redirect(['tasks/view', 'id' => $id]);
    }

    public function actionDeny(int $id): Response
    {
        $task = $this->findOrDie($id, Task::class);
        $rating = $this->findOrDie($task->performer_id, Rating::class);
        $performer = $this->findOrDie($task->performer_id, Performer::class);

        $task->goToNextStatus(new DenyAction());
        $rating->increaseFailedTasks();
        $rating->updatePerformerRating();

        $performer->status_id = PerformerStatus::PERFORMER_FREE;
        $performer->save();

        return $this->redirect(['tasks/view', 'id' => $id]);
    }

    public function actionMy(): Response|string
    {
        $tasksQuery = Task::find();
        $tag = Yii::$app->request->get('tag');

        $availableTags =
            Yii::$app->user->identity->isPerformer ?
                ['progress', 'expired', 'closed', null]
                :
                ['new', 'progress', 'closed', null];

        if (!in_array($tag, $availableTags)) {
            return $this->redirect('error/notfound');
        }

        switch ($tag) {
            case 'new':
                $tasksQuery->andWhere(
                    ['client_id' => Yii::$app->user->id, 'taskStatus_id' => [AvailableActions::STATUS_NEW]]
                );
                break;
            case 'progress':
                $tasksQuery->andWhere(
                    ['client_id' => Yii::$app->user->id, 'taskStatus_id' => AvailableActions::STATUS_IN_PROGRESS]
                );
                break;
            case 'closed':
                if (!Yii::$app->user->identity->isPerformer) {
                    $tasksQuery->andWhere(
                        [
                            'client_id' => Yii::$app->user->id,
                            'taskStatus_id' => [
                                AvailableActions::STATUS_CANCEL,
                                AvailableActions::STATUS_EXPIRED,
                                AvailableActions::STATUS_COMPLETE
                            ]
                        ]
                    );
                } else {
                    $tasksQuery->andWhere(
                        [
                            'performer_id' => Yii::$app->user->id,
                            'taskStatus_id' => [AvailableActions::STATUS_EXPIRED, AvailableActions::STATUS_COMPLETE]
                        ]
                    );
                }
                break;
            case 'expired':
                $now = new Expression('NOW()');
                $tasksQuery->andWhere(
                    ['performer_id' => Yii::$app->user->id, 'taskStatus_id' => [AvailableActions::STATUS_EXPIRED]]
                )->andWhere(['<', 'deadline', $now]);
                break;
            default:
                if (!Yii::$app->user->identity->isPerformer) {
                    $tasksQuery->andWhere(['client_id' => Yii::$app->user->id]);
                } else {
                    $tasksQuery->andWhere(['performer_id' => Yii::$app->user->id]);
                }
        }

        $pages = new Pagination([
            'totalCount' => $tasksQuery->count(),
            'pageSize' => 5,
            'forcePageParam' => false,
            'pageSizeParam' => false,
        ]);

        $tasks = $tasksQuery->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('my', ['tasks' => $tasks, 'tag' => $tag, 'pages' => $pages]);
    }
}