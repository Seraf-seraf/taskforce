<?php

namespace app\models;

use app\helpers\YandexMapHelper;
use DateTime;
use TaskForce\logic\actions\AbstractAction;
use TaskForce\logic\AvailableActions;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int $client_id
 * @property int $taskStatus_id
 * @property int $category_id
 * @property int $city_id
 * @property string|null $uid
 * @property string $name
 * @property string|null $location
 * @property int|null $budget
 * @property string $deadline
 * @property string|null $finished
 * @property string $description
 * @property string $dateCreate
 */
class Task extends \yii\db\ActiveRecord
{
    public ?bool $noLocation;
    public ?bool $noResponses;
    public null|int|string $filterPeriod;

    public function __construct()
    {
        $this->noLocation = null;
        $this->noResponses = null;
        $this->filterPeriod = null;
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['category_id', 'name', 'description', 'deadline'], 'required'],
            [['taskStatus_id', 'category_id', 'budget'], 'integer'],
            [['client_id'], 'default', 'value' => Yii::$app->user->id],
            [['city_id'], 'default', 'value' => Yii::$app->user->identity->city_id],
            [['taskStatus_id'], 'default', 'value' => 1],
            [['finished', 'dateCreate'], 'safe'],
            [['deadline'], 'date', 'format' => 'php:Y-m-d', 'min' => date('Y-m-d', strtotime('+1 day')), 'minString' => 'чем текущий день'],
            [['name'], 'string', 'length' => [5, 32]],
            [['noLocation'], 'boolean'],
            [['noResponses'], 'boolean'],
            [['filterPeriod'], 'integer'],
            [['location'], 'string', 'max' => 255],
            [
                ['description'],
                'string',
                'length' => [10, 200],
                'tooShort' => 'Описание задания должно быть длиной минимум 10 символов'
            ],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['client_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskCategories::class, 'targetAttribute' => ['category_id' => 'id']],
            [['taskStatus_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskStatus::class, 'targetAttribute' => ['taskStatus_id' => 'id']],
            [
                ['performer_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Performer::class,
                'targetAttribute' => ['performer_id' => 'performer_id']
            ],
            [
                ['city_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => City::class,
                'targetAttribute' => ['city_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'taskStatus_id' => 'Task Status ID',
            'uid' => 'Uid',
            'name' => 'Название',
            'category_id' => 'Категория',
            'location' => 'Локация',
            'deadline' => 'Срок исполнения',
            'finished' => 'Finished',
            'description' => 'Подробности задания',
            'dateCreate' => 'Date Create',
            'noLocation' => 'Удаленная работа',
            'noResponses' => 'Без откликов',
            'budget' => 'Бюджет',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory(): \yii\db\ActiveQuery
    {
        return $this->hasOne(TaskCategories::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'client_id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Comments::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles(): \yii\db\ActiveQuery
    {
        return $this->hasMany(File::class, ['task_uid' => 'uid']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponses(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Response::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[TaskStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskStatus(): \yii\db\ActiveQuery
    {
        return $this->hasOne(TaskStatus::class, ['id' => 'taskStatus_id']);
    }

    public function getPerformer(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Performer::class, ['performer_id' => 'performer_id']);
    }

    public function getCity(): \yii\db\ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    public function getSearchQuery(): \yii\db\ActiveQuery
    {
        $query = self::find();
        $query->andWhere(['taskStatus_id' => TaskStatus::STATUS_NEW]);
        $query->andFilterWhere(
            [
                'category_id' => Yii::$app->request->get('category_id') ? Html::encode(
                    Yii::$app->request->get('category_id')
                ) : $this->category_id
            ]
        );

        $query->andWhere([
            'or',
            ['task.city_id' => Yii::$app->user->identity->city_id],
            ['location' => ''],
        ]);

        if ($this->noLocation) {
            $query->andWhere(['location' => '']);
        }

        if ($this->noResponses) {
            $query->leftJoin('response', 'response.task_id = task.id')
                  ->andWhere(['response.id' => null]);
        }

        if ($this->filterPeriod) {
            $query->andFilterWhere(['>', 'UNIX_TIMESTAMP(task.dateCreate)', time() - $this->filterPeriod]);
        }

        return $query->orderBy(['task.dateCreate' => SORT_DESC]);
    }

    public function goToNextStatus(AbstractAction $action): void
    {
        $performer_id = $this->performer?->performer_id;

        $actionManager = new AvailableActions($this->taskStatus_id, $this->client_id, $performer_id);

        $this->taskStatus_id = $actionManager->getNextStatus($action);

        $this->save(false);
    }

    public function setFinishDate(): void
    {
        $now = new DateTime();
        $this->finished = $now->format('Y-m-d H:i:s');
        $this->save(false);
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);

        if ($this->location) {
            $mapHelper = new YandexMapHelper('e666f398-c983-4bde-8f14-e3fec900592a');
            $coords = $mapHelper->getCoords($this->city->name, $this->location);

            if ($coords) {
                $this->lat = $coords[0];
                $this->long = $coords[1];
            } else {
                return $this->addError('location', 'Задание должно выполняться в вашем городе, либо удаённо');
            }
        }
        return true;
    }
}
