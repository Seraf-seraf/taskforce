<?php

namespace app\models;

use yii\db\Query;

/**
 * This is the model class for table "performer".
 *
 * @property int $performer_id
 * @property int|null $status_id
 */
class Performer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'performer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['performer_id'], 'required'],
            [['performer_id', 'status_id'], 'integer'],
            [['performer_id'], 'unique'],
            [['performer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['performer_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => PerformerStatus::class, 'targetAttribute' => ['status_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'performer_id' => 'Performer ID',
            'status_id' => 'Status ID',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'performer_id']);
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Task::class, ['performer_id' => 'performer_id']);
    }

    /**
     * Gets query for [[Rating]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRating(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Rating::class, ['performer_id' => 'performer_id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus(): \yii\db\ActiveQuery
    {
        return $this->hasOne(PerformerStatus::class, ['id' => 'status_id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Comments::class, ['performer_id' => 'performer_id']);
    }
}
