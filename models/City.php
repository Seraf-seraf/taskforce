<?php

namespace app\models;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $name
 * @property float $lat
 * @property float $long
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'city';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'lat', 'long'], 'required'],
            [['lat', 'long'], 'number'],
            [['name'], 'string', 'max' => 36],
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
            'lat' => 'Lat',
            'long' => 'Long',
        ];
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers(): \yii\db\ActiveQuery
    {
        return $this->hasMany(User::class, ['city_id' => 'id']);
    }
}
