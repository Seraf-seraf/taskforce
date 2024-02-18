<?php

namespace app\models;

use Yii;
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
 * @property int|null $isPerformer
 * @property string|null $dateRegistration
 *
 * @property City $city
 * @property Comments[] $comments
 * @property Performer[] $performers
 * @property Response[] $responses
 * @property Task[] $tasks
 * @property UserSettings[] $userSettings
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{

    public $password_repeat;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'password', 'city_id'], 'required'],
            [['city_id', 'isPerformer'], 'integer'],
            [['dateRegistration'], 'safe'],
            [
                'email',
                'unique',
                'message' => 'Пользователь с этой почтой уже зарегистрирован',
            ],
            [['name', 'email'], 'string', 'max' => 64],
            ['email', 'email'],
            [['password'], 'string', 'min' => 8, 'max' => 128],
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name'             => 'Имя',
            'email'            => 'Электронная почта',
            'password'         => 'Пароль',
            'password_repeat'  => 'Повторите пароль',
            'city_id'          => 'Город',
            'isPerformer'      => 'Я собираюсь откликаться на заказы',
            'dateRegistration' => 'Date Registration',
        ];
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comments::class, ['performer_id' => 'id']);
    }

    /**
     * Gets query for [[Comments0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments0()
    {
        return $this->hasMany(Comments::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[PerformerStatuses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPerformerStatuses()
    {
        return $this->hasOne(PerformerStatus::class, ['performer_id' => 'id']);
    }

    /**
     * Gets query for [[Rating]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRating()
    {
        return $this->hasOne(Rating::class, ['performer_id' => 'id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponses()
    {
        return $this->hasMany(Response::class, ['performer_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[UserSettings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserSettings()
    {
        return $this->hasOne(UserSettings::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Performer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPerformer()
    {
        return $this->hasOne(Performer::class, ['performer_id' => 'id']);
    }

    /**
     * Расчет рейтинга пользователя
     *
     * @param  int  $id
     *
     * @return float
     */
    public static function calculateRating(int $id): float
    {
        $totalMark = Comments::find()
                             ->where(['performer_id' => $id])
                             ->sum('mark');

        $totalComments = Comments::find()
                                 ->where(['performer_id' => $id])
                                 ->count();

        $failedTasks = Rating::find()
                             ->select('failedTasks')
                             ->where(['performer_id' => $id])
                             ->scalar();

        return $totalMark / ($totalComments + $failedTasks);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword(
            $password,
            $this->password
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
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
    public static function findByUsername($username)
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
    public function getAuthKey()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return true;
    }

}
