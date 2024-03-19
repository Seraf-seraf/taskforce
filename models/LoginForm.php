<?php

namespace app\models;

use yii\base\Model;

class LoginForm extends Model
{

    public ?string $email;

    public ?string $password;

    private ?User $_user;

    public function __construct()
    {
        $this->email = null;
        $this->password = null;
        $this->_user = null;
    }

    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['email'], 'email', 'message' => 'Неправильный формат электронной почты'],
            [['password'], 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'E-mail',
            'password' => 'Пароль',
        ];
    }

    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неправильный email или пароль');
            }
        }
    }

    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }

}