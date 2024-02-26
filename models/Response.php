<?php

namespace app\models;

/**
 * This is the model class for table "response".
 *
 * @property int $id
 * @property int $performer_id
 * @property int $task_id
 * @property int|null $price
 * @property string|null $comment
 * @property string $dateCreate
 * @property int $isRejected
 * @property int $isHolded
 *
 * @property Performer $performer
 * @property Task $task
 */
class Response extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'response';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['performer_id', 'task_id', 'dateCreate'], 'required'],
            [['performer_id', 'task_id', 'price', 'isRejected'], 'integer'],
            [['dateCreate'], 'safe'],
            [['comment'], 'string', 'max' => 128],
            [['performer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Performer::class, 'targetAttribute' => ['performer_id' => 'performer_id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'performer_id' => 'Performer ID',
            'task_id' => 'Task ID',
            'price' => 'Price',
            'comment' => 'Comment',
            'dateCreate' => 'Date Create',
            'isRejected' => 'Is Rejected',
        ];
    }

    /**
     * Gets query for [[Performer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPerformer()
    {
        return $this->hasOne(User::class, ['id' => 'performer_id']);
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }
}
