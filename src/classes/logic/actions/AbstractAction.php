<?php
namespace TaskForce\logic\actions;

abstract class AbstractAction 
{
    /**
     * Возвращает лейбл для действия
     * @return string
     */
    abstract public static function getLabel(): string;


    /**
     * Возвращает имя действия
     * @return string  
     */
    abstract public static function getActionName(): string;


    /**
     * Проверяет право на выполнение действия
     * @return bool
     */
    abstract public static function checkRights(int $userID, int $cliendID, ?int $performerID): bool;
}