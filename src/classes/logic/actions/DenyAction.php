<?php
namespace TaskForce\logic\actions;

require_once 'vendor/autoload.php';

class DenyAction extends AbstractAction
{
    public static function getLabel(): string
    {
        return 'Отказаться';
    }

    public static function getActionName(): string
    {
        return 'action_deny';
    }

    public static function checkRights(int $userID, int $cliendID, ?int $performerID): bool
    {
        return $userID == $performerID;
    }
}