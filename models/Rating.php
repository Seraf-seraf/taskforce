<?php

namespace app\models;

/**
 * This is the model class for table "rating".
 *
 * @property int $performer_id
 * @property float $userRating
 * @property int $failedTasks
 * @property int $finishedTasks
 */
class Rating extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'rating';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
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
    public function attributeLabels(): array
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
    public function getPerformer(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Performer::class, ['performer_id' => 'performer_id']);
    }

    public function increaseFailedTasks(): void
    {
        $this->failedTasks += 1;
        $this->save();
    }

    public function increaseFinishedTasks(): void
    {
        $this->finishedTasks += 1;
        $this->save();
    }

    public function updatePerformerRating(): void
    {
        $totalMarks = Comments::find()->where(['performer_id' => $this->performer_id])->sum('mark');
        $totalComments = Comments::find()->where(['performer_id' => $this->performer_id])->count();

        $rating = $totalMarks / ($totalComments + $this->failedTasks);

        $this->userRating = max($rating, 0);
        $this->save();
    }

    public static function getRatingPosition(int $id): int
    {
        return (new Query())
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
    }
}
