<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $city_id
 * @property int $isPerformer
 * @property string $dateRegistration
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{

    public ?string $password_repeat;


    public ?string $old_password;
    public ?string $new_password;
    public ?string $new_password_repeat;

    public function __construct()
    {
        $this->password_repeat = null;
        $this->old_password = null;
        $this->new_password = null;
        $this->new_password_repeat = null;
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'email', 'password', 'city_id'], 'required', 'on' => 'insert'],
            [['city_id', 'isPerformer'], 'integer'],
            [['dateRegistration'], 'safe'],
            [
                'email',
                'unique',
                'message' => 'Пользователь с этой почтой уже зарегистрирован',
            ],
            [['name', 'email'], 'string', 'max' => 64],
            [['email'], 'email'],
            [['password'], 'string', 'min' => 8, 'max' => 64],
            [
                'password_repeat',
                'compare',
                'compareAttribute' => 'password',
                'message'          => 'Значение не совпадает с полем пароль',
            ],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name'             => 'Имя',
            'email'            => 'Электронная почта',
            'password'         => 'Пароль',
            'password_repeat'  => 'Повторите пароль',
            'city_id'          => 'Город',
            'isPerformer'      => 'Я собираюсь откликаться на заказы',
            'dateRegistration' => 'Date Registration'
        ];
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity(): \yii\db\ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Comments::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Rating]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRating(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Rating::class, ['performer_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Task::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[UserSettings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserSettings(): \yii\db\ActiveQuery
    {
        return $this->hasOne(UserSettings::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Performer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPerformer(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Performer::class, ['performer_id' => 'id']);
    }

    public function validatePassword($password): bool
    {
        return Yii::$app->security->validatePassword(
            $password,
            $this->password
        );
    }

    /**
     * @throws Exception
     */
    public function setPassword($password): string
    {
        return Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id): User|IdentityInterface|null
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param  string  $username
     *
     * @return static|null
     */
    public static function findByUsername(string $username): static|null
    {
        return static::findOne(['email' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): ?bool
    {
        return true;
    }
}
