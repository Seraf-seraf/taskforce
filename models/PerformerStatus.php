<?php

namespace app\models;

/**
 * This is the model class for table "performerStatus".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property Performer[] $performers
 */
class PerformerStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'performerStatus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
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
    public function getPerformers()
    {
        return $this->hasMany(Performer::class, ['status_id' => 'id']);
    }
}
