<?php

namespace app\helpers;

use app\models\Performer;
use app\models\Task;
use app\models\User;
use TaskForce\exceptions\StatusActionException;
use TaskForce\logic\actions\CancelAction;
use TaskForce\logic\actions\CompleteAction;
use TaskForce\logic\actions\DenyAction;
use TaskForce\logic\actions\ResponseAction;
use TaskForce\logic\AvailableActions;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Вспомогательный класс для работы с интерфесом сайта
 */
class UIHelper
{

    /**
     * Плюрализация для русских слов.
     *
     * @param  string  $n  int целое число
     * @param array $forms  принимает массив с 3 формами слова: в единственном
     *               числе и 2 формы множественного числа
     *
     * @return string
     */
    public static function pluralize(string $n, array $forms): string
    {
        return $n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]);
    }

    /**
     * Создание контейнера со звездочками рейтинга
     *
     * @param  string  $size  small or big
     * @param float|null $rating
     *
     * @return string
     */
    public static function getStarsByRate(
        string $size = 'small',
        float $rating = 0,
        bool $active = false
    ): string {
        $stars = "<div class='stars-rating $size'>";

        if ($active) {
            $stars = "<div class='stars-rating $size active-stars'>";
            for ($i = 1; $i < 6; $i++) {
                $stars .= "<span type='radio' id='star' data-star='$i'></span>";
            }
        } else {
            for ($i = 0; $i < 5; $i++) {
                if ($i < $rating) {
                    $stars .= "<span class='fill-star'></span>";
                } else {
                    $stars .= '<span></span>';
                }
            }
        }

        $stars .= "</div>";

        return $stars;
    }

    /**
     *  Генерация кнопок для действий с заданием
     *
     * @param  Task  $task
     * @param  User  $user
     * @param  \app\models\Performer|null  $performer
     *
     * @return array $buttons
     * @throws \TaskForce\exceptions\StatusActionException
     */
    public static function getActionButtons(Task $task, User $user, Performer $performer = null)
    {
        $buttons = [];

        $colors = [
            CancelAction::class => 'orange',
            CompleteAction::class => 'yellow',
            DenyAction::class => 'orange',
            ResponseAction::class => 'blue'
        ];

        try {
            $userRole = $user->isPerformer ? AvailableActions::ROLE_PERFORMER : AvailableActions::ROLE_CLIENT;
            $performerID = $performer?->performer_id;

            $availableActionsManager = new AvailableActions($task->taskStatus_id, $task->client_id, $performerID);

            $actions = $availableActionsManager->getAvailableActions($userRole, $user->id);

            foreach ($actions as $action) {
                $label = $action::getLabel();

                $options = [
                    'class' => "button action-btn button--".$colors[$action],
                    'data-action' => $action::getActionName()
                ];

                if ($action == CancelAction::class) {
                    $options += ['href' => Url::toRoute(['tasks/cancel', 'id' => $task->id])];
                }

                $buttons[] = Html::tag('a', $label, $options);
            }
        } catch (StatusActionException $exception) {
            error_log($exception->getMessage());
        }

        return $buttons;
    }

}