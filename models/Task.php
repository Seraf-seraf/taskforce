<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int $client_id
 * @property int $taskStatus_id
 * @property int $category_id
 * @property string|null $uid
 * @property string $name
 * @property string|null $location
 * @property int|null $budget
 * @property string|null $deadline
 * @property string|null $finished
 * @property string|null $description
 * @property string|null $dateCreate
 *
 * @property TaskCategories $category
 * @property User $client
 * @property Comments[] $comments
 * @property File[] $files
 * @property Response[] $responses
 * @property TaskStatus $taskStatus
 */
class Task extends \yii\db\ActiveRecord
{
    public $noLocation;
    public $noResponses;
    public $filterPeriod;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'name', 'description', 'deadline'], 'required'],
            [['taskStatus_id', 'category_id', 'budget'], 'integer'],
            [['client_id'], 'default', 'value' => Yii::$app->user->id],
            [['taskStatus_id'], 'default', 'value' => 1],
            [['finished', 'dateCreate'], 'safe'],
            [['deadline'], 'date', 'format' => 'php:Y-m-d', 'min' => date('Y-m-d', strtotime('+1 day')), 'minString' => 'чем текущий день'],
            [['name'], 'string', 'max' => 64],
            [['noLocation'], 'boolean'],
            [['noResponses'], 'boolean'],
            [['filterPeriod'], 'integer'],
            [['location'], 'string', 'max' => 128],
            [['description'], 'string', 'length' => [30, 200], 'tooShort' => 'Описание задания должно быть длиной минимум 30 символов'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['client_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskCategories::class, 'targetAttribute' => ['category_id' => 'id']],
            [['taskStatus_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskStatus::class, 'targetAttribute' => ['taskStatus_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
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
    public function getCategory()
    {
        return $this->hasOne(TaskCategories::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(User::class, ['id' => 'client_id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comments::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(File::class, ['task_uid' => 'uid']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponses()
    {
        return $this->hasMany(Response::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[TaskStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskStatus()
    {
        return $this->hasOne(TaskStatus::class, ['id' => 'taskStatus_id']);
    }

    public function getPerformer()
    {
        return $this->hasOne(Performer::class, ['task_id' => 'id']);
    }

    public function getSearchQuery(): \yii\db\ActiveQuery
    {
        $query = self::find();
        $query->andWhere(['taskStatus_id' => TaskStatus::STATUS_NEW]);
        $query->andFilterWhere(['category_id' => $this->category_id]);

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
}
