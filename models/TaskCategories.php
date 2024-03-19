<?php

namespace app\models;

/**
 * This is the model class for table "taskCategories".
 *
 * @property int $id
 * @property string $name
 * @property string|null $icon
 */
class TaskCategories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'taskCategories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name', 'icon'], 'string', 'max' => 36],
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
            'icon' => 'Icon',
        ];
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Task::class, ['category' => 'id']);
    }
}
