<?php

namespace app\models;

/**
 * This is the model class for table "taskStatus".
 *
 * @property int $id
 * @property string $name
 */
class TaskStatus extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'taskStatus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Task::class, ['task_status' => 'id']);
    }
}
