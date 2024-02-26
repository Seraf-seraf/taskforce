<?php
namespace TaskForce\logic\actions;

interface AbstractAction
{
    /**
     * Возвращает лейбл для действия
     * @return string
     */
    public static function getLabel(): string;


    /**
     * Возвращает имя действия
     * @return string  
     */
    public static function getActionName(): string;

    /**
     * Проверяет право на выполнение действия
     *
     * @param  int  $userID ID пользователя, который залогинен на странице
     * @param  int  $clientID ID заказчика
     * @param  int|null  $performerID ID исполнителя
     *
     * @return bool
     */
    public static function checkRights(int $userID, int $clientID, ?int $performerID): bool;
}