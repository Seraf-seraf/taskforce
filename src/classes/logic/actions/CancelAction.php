<?php
namespace TaskForce\logic\actions;

require_once 'vendor/autoload.php';

class CancelAction extends AbstractAction
{
    public static function getLabel(): string
    {
        return 'Отменить';
    }

    public static function getActionName(): string
    {
        return 'action_cancel';
    }

    public static function checkRights(int $userID, int $cliendID, ?int $performerID): bool
    {
        return $userID == $cliendID;
    }
}