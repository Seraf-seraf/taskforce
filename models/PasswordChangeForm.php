<?php

namespace app\models;

use yii\base\Model;

/**
 * Password reset form
 */
class PasswordChangeForm extends Model
{
    public ?string $currentPassword;
    public ?string $newPassword;
    public ?string $newPasswordRepeat;

    /**
     * @var User
     */
    private User $_user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->_user = $user;
        $this->currentPassword = null;
        $this->newPassword = null;
        $this->newPasswordRepeat = null;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['currentPassword', 'newPassword', 'newPasswordRepeat'], 'required', 'skipOnEmpty' => true],
            ['currentPassword', 'validatePassword'],
            ['newPassword', 'string', 'min' => 8],
            ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'currentPassword' => 'Старый пароль',
            'newPassword' => 'Новый пароль',
            'newPasswordRepeat' => 'Повторите новый пароль'
        ];
    }

    /**
     * @param string $attribute
     * @param array|null $params
     */
    public function validatePassword(string $attribute, ?array $params): void
    {
        if (!$this->hasErrors()) {
            if (!$this->_user->validatePassword($this->$attribute)) {
                $this->addError($attribute, 'Неверный текущий пароль');
            }
        }
    }

    /**
     * @return bool
     */
    public function changePassword(): bool
    {
        if ($this->validate()) {
            $user = $this->_user;
            $user->password = $user->setPassword($this->newPassword);
            return $user->save(false);
        } else {
            return false;
        }
    }
}