<?php
namespace TaskForce\logic\actions;

class ResponseAction implements AbstractAction
{
    public static function getLabel(): string
    {
        return 'Откликнуться';
    }

    public static function getActionName(): string
    {
        return 'action_response';
    }

    public static function checkRights(int $userID, int $clientID, ?int $performerID): bool
    {
        return $userID !== $performerID;
    }
}