<?php

namespace app\models;

/**
 * This is the model class for table "performerStatus".
 *
 * @property int $id
 * @property string $name
 */
class PerformerStatus extends \yii\db\ActiveRecord
{
    public const PERFORMER_FREE = 1;
    public const PERFORMER_BUSY = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'performerStatus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'   => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[Performers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPerformers(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Performer::class, ['status_id' => 'id']);
    }
}
