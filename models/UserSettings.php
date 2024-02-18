<?php

namespace app\models;

/**
 * This is the model class for table "userSettings".
 *
 * @property int $user_id
 * @property string|null $avatar
 * @property string|null $phone
 * @property string|null $telegram
 * @property string|null $birthday
 * @property string|null $description
 * @property int $privateContacts
 *
 * @property User $user
 */
class UserSettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userSettings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'privateContacts'], 'integer'],
            [['birthday'], 'safe'],
            [['description'], 'string'],
            [['avatar'], 'string', 'max' => 255],
            [['phone'], 'string', 'pattern' => '/^[0-9]{11}$/'],
            [['telegram'], 'string', 'min' => 5, 'max' => 64, 'pattern' => '/^[a-zA-Z0-9_]+$/'],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'avatar' => 'Avatar',
            'phone' => 'Phone',
            'telegram' => 'Telegram',
            'birthday' => 'Birthday',
            'description' => 'Description',
            'privateContacts' => 'Private Contacts',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
