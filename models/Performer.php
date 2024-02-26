<?php

namespace app\models;

use yii\db\Query;

/**
 * This is the model class for table "performer".
 *
 * @property int $performer_id
 * @property int|null $status_id
 * @property int|null $task_id
 *
 * @property User $performer
 * @property Rating $rating
 * @property PerformerStatus $status
 */
class Performer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'performer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['performer_id'], 'required'],
            [['performer_id', 'status_id', 'task_id'], 'integer'],
            [['performer_id'], 'unique'],
            [['performer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['performer_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => PerformerStatus::class, 'targetAttribute' => ['status_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'performer_id' => 'Performer ID',
            'status_id' => 'Status ID',
            'task_id' => 'Task ID'
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
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

    /**
     * Gets query for [[Rating]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRating()
    {
        return $this->hasOne(Rating::class, ['performer_id' => 'performer_id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(PerformerStatus::class, ['id' => 'status_id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comments::class, ['performer_id' => 'performer_id']);
    }


    public static function getRatingPosition($id)
    {
        $place = (new Query())
            ->select(['COUNT(performer_id) + 1'])
            ->from('rating')
            ->where([
                '>',
                'rating.userRating',
                (new Query())
                    ->select('userRating')
                    ->from('rating')
                    ->where(['performer_id' => $id]),
            ])
            ->orderBy(['userRating' => 'DESC'])
            ->scalar();

        return $place;
    }
}
