<?php
namespace TaskForce\logic\actions;

require_once 'vendor/autoload.php';

class CompleteAction extends AbstractAction
{
    public static function getLabel(): string
    {
        return 'Завершить';
    }

    public static function getActionName(): string
    {
        return 'action_complete';
    }

    public static function checkRights(int $userID, int $cliendID, ?int $performerID): bool
    {
        print_r($cliendID);
        return $userID == $performerID;
    }
}