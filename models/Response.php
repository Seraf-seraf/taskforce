<?php

namespace app\models;

/**
 * This is the model class for table "response".
 *
 * @property int $id
 * @property int $performer_id
 * @property int $task_id
 * @property int $price
 * @property string $comment
 * @property string $dateCreate
 * @property int $isRejected
 */
class Response extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'response';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['task_id', 'price', 'isRejected'], 'integer'],
            [['comment', 'price'], 'required'],
            [['price'], 'integer', 'min' => 0, 'max' => 100000, 'message' => 'Число должно быть целым положительным'],
            [
                ['comment'],
                'unique',
                'targetAttribute' => ['performer_id', 'task_id'],
                'message' => 'Вы уже оставляли отклик'
            ],
            [['dateCreate'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['comment'], 'string', 'max' => 128],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
            [
                ['performer_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Performer::class,
                'targetAttribute' => ['performer_id' => 'performer_id']
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
            'performer_id' => 'Performer ID',
            'task_id' => 'Task ID',
            'price' => 'Стоимость',
            'comment' => 'Ваш комментарий',
            'dateCreate' => 'Date Create',
            'isRejected' => 'Is Rejected',
        ];
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
