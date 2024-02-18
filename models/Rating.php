<?php

namespace app\models;

/**
 * This is the model class for table "rating".
 *
 * @property int $performer_id
 * @property float|null $userRating
 * @property int $failedTasks
 * @property int $finishedTasks
 *
 * @property Performer $performer
 */
class Rating extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rating';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['performer_id'], 'required'],
            [['performer_id', 'failedTasks', 'finishedTasks'], 'integer'],
            [['userRating'], 'number'],
            [['performer_id'], 'unique'],
            [['performer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Performer::class, 'targetAttribute' => ['performer_id' => 'performer_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'performer_id' => 'Performer ID',
            'userRating' => 'User Rating',
            'failedTasks' => 'Failed Tasks',
            'finishedTasks' => 'Finished Tasks',
        ];
    }

    /**
     * Gets query for [[Performer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPerformer()
    {
        return $this->hasOne(Performer::class, ['performer_id' => 'performer_id']);
    }
}
