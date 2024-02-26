<?php
namespace TaskForce\logic\actions;

class DenyAction implements AbstractAction
{
    public static function getLabel(): string
    {
        return 'Отказаться';
    }

    public static function getActionName(): string
    {
        return 'action_deny';
    }

    public static function checkRights(int $userID, int $clientID, ?int $performerID): bool
    {
        return $userID == $performerID;
    }
}