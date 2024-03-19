<?php

namespace TaskForce\logic\actions;

class CompleteAction implements AbstractAction
{
    public static function getLabel(): string
    {
        return 'Завершить';
    }

    public static function getActionName(): string
    {
        return 'action_complete';
    }

    public static function checkRights(int $userID, int $clientID, ?int $performerID): bool
    {
        return $userID == $clientID;
    }
}