<?php

namespace app\models;

use DateTime;
use yii\web\UploadedFile;

/**
 * This is the model class for table "userSettings".
 *
 * @property int $user_id
 * @property string $avatar
 * @property string|null $phone
 * @property string|null $telegram
 * @property string|null $birthday
 * @property string|null $description
 * @property int $privateContacts
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
            [['user_id'], 'required', 'on' => 'insert'],
            [['privateContacts'], 'integer', 'on' => 'update'],
            [['birthday'], 'date', 'format' => 'php:Y-m-d'],
            [['description'], 'string'],
            [['avatar'], 'file', 'mimeTypes' => ['image/*'], 'on' => 'insert'],
            [['phone'], 'match', 'pattern' => '/^[0-9]{11}$/', 'message' => 'Ведите номер в формате 7**********'],
            [
                ['telegram'],
                'match',
                'pattern' => '/^[@]{1}[A-Za-z\d_]{5,32}$/',
                'message' => 'Введите имя пользователя в формате @your_name'
            ],
            [
                ['categories_id'],
                'in',
                'range' => array_column(TaskCategories::find()->all(), 'id'),
                'allowArray' => true,
                'message' => 'Выберите правильные категории'
            ],
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
            'phone' => 'Номер телефона',
            'telegram' => 'Telegram',
            'birthday' => 'День рождения',
            'description' => 'Информация о себе',
            'privateContacts' => 'Показать контакты только заказчикам',
            'categories_id' => 'Выбор специализаций',
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

    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Получение возраста пользователя
     *
     * @return int
     * @throws \Exception
     */
    public function getAge()
    {
        $now = new DateTime('now');
        $birthday = new DateTime($this->birthday);
        return $now->diff($birthday)->y;
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);

        $avatar = UploadedFile::getInstance($this, 'avatar');

        if (!empty($avatar)) {
            $name = uniqid() . '.' . $avatar->getExtension();
            $path = '/uploads/' . $name;

            $avatar->saveAs('@webroot/uploads/' . $name);
            $this->avatar = $path;
        }
        return true;
    }
}
