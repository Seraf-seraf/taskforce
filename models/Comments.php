<?php

namespace app\models;

/**
 * This is the model class for table "comments".
 *
 * @property int $id
 * @property int $performer_id
 * @property int $client_id
 * @property int $task_id
 * @property int $mark
 * @property string $comment
 */
class Comments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'comments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['comment', 'mark'], 'required'],
            [['mark'], 'integer', 'min' => 1, 'max' => 5],
            [
                ['comment'],
                'unique',
                'targetAttribute' => ['task_id', 'performer_id'],
                'message' => 'Вы уже осталвляли отзыв об этом исполнителе!'
            ],
            [['comment'], 'string', 'length' => [10, 128]],
            [
                ['performer_id'],
                'exist',
                'targetClass' => Performer::class,
                'targetAttribute' => ['performer_id' => 'performer_id']
            ],
            [
                ['client_id'],
                'exist',
                'targetClass' => Task::class,
                'targetAttribute' => ['client_id' => 'client_id']
            ],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'performer_id' => 'Performer ID',
            'client_id' => 'Client ID',
            'task_id' => 'Task ID',
            'mark' => 'Оценка',
            'comment' => 'Отзыв о работе',
        ];
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
     * Gets query for [[Performer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPerformer(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Performer::class, ['performer_id' => 'performer_id']);
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }
}
