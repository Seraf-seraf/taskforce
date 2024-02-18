<?php
namespace TaskForce\logic\actions;

require_once 'vendor/autoload.php';

class ResponseAction extends AbstractAction
{
    public static function getLabel(): string
    {
        return 'Откликнуться';
    }

    public static function getActionName(): string
    {
        return 'action_response';
    }

    public static function checkRights(int $userID, int $cliendID, ?int $performerID): bool
    {
        return $userID !== $performerID;
    }
}