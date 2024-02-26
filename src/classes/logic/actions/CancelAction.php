<?php
namespace TaskForce\logic\actions;

class CancelAction implements AbstractAction
{
    public static function getLabel(): string
    {
        return 'Отменить';
    }

    public static function getActionName(): string
    {
        return 'action_cancel';
    }

    public static function checkRights(int $userID, int $clientID, ?int $performerID): bool
    {
        return $userID == $clientID;
    }
}