<?php

namespace app\models;

/**
 * This is the model class for table "taskStatus".
 *
 * @property int $id
 * @property string $name
 *
 * @property Task[] $tasks
 */
class TaskStatus extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'taskStatus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
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
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['task_status' => 'id']);
    }
}
