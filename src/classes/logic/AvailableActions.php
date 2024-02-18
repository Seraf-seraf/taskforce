<?php
namespace TaskForce\logic;

use DateTime;
use TaskForce\exceptions\StatusActionException;
use TaskForce\logic\actions\AbstractAction;
use TaskForce\logic\actions\CancelAction;
use TaskForce\logic\actions\CompleteAction;
use TaskForce\logic\actions\DenyAction;
use TaskForce\logic\actions\ResponseAction;

require_once 'vendor/autoload.php';

class AvailableActions
{
    const STATUS_NEW = 'new';
    const STATUS_CANCEL = 'canceled';
    const STATUS_IN_PROGRESS = 'proceed';
    const STATUS_COMPLETE = 'complete';
    const STATUS_EXPIRED = 'expired';

    const ROLE_PERFORMER = 'performer';
    const ROLE_CLIENT = 'client';

    private DateTime $_finishDate;

    private ?int $_performerID;
    private int $_clientID;
    private string $_status;


    /**
     * Конструктор задачи и ее статуса
     *
     * @param string   $status 
     * @param int      $cliendID 
     * @param null|int $performerID
     * 
     * @throws StatusActionExeption
     * 
     * @return void 
     */
    public function __construct(string $status, int $clientID, ?int $performerID = null)
    {
        $this->_setStatus($status);

        $this->_performerID = $performerID;
        $this->_clientID = $clientID;
    }


    /**
     * Возвращает действия, которые доступны для пользователя
     * в зависимости от его роли и статуса задачи
     *
     * @param string $role Роль пользователя: Исполнитель или Заказчик
     * @param int    $id   ID пользователя     
     *
     * @return array
     */
    public function getAvailableActions(string $role, int $id): array
    {
        $this->checkRole($role);

        $statusActions = $this->_statusAllowedActions()[$this->_status];
        $roleActions = $this->_roleAllowedActions()[$role];

        $allowedActions = array_intersect($statusActions, $roleActions);
        
        $allowedActions = array_filter(
            $allowedActions, function ($action) use ($id) {
                return $action::checkRights($id, $this->_clientID, $this->_performerID);
            }
        );

        return array_values($allowedActions);
    }


    /**
     * Устанавливает статус
     * 
     * @param string $status Статус задачи
     * 
     * @return void
     */
    private function _setStatus($status): void
    {
        $availableStatuses = [
            self::STATUS_NEW,
            self::STATUS_CANCEL,
            self::STATUS_COMPLETE,
            self::STATUS_IN_PROGRESS,
            self::STATUS_EXPIRED,
        ];

        if (in_array($status, $availableStatuses)) {
            $this->_status = $status;
        }
    }


    /**
     * Устанавливает следущий статус 
     * после выполнения определенного действия
     *
     * @param AbstractAction $action Принимает класс действия
     * 
     * @return null|string
     */
    public function getNextStatus(AbstractAction $action): ?string
    {
        $map = [
            CompleteAction::class => self::STATUS_COMPLETE,
            CancelAction::class => self::STATUS_CANCEL,
            DenyAction::class => self::STATUS_CANCEL
        ];

        if (!in_array(get_class($action), $map)) {
            throw new StatusActionException("Ошибка! Передайте другой класс действия!");
        }

        return $map[get_class($action)];
    }


    /**
     * Возвращает действия, доступные для опредленного статуса
     *  
     * @return array
     */
    private function _statusAllowedActions(): array
    {
        $map = [
            self::STATUS_IN_PROGRESS => [DenyAction::class, CompleteAction::class],
            self::STATUS_NEW => [CancelAction::class, ResponseAction::class]
        ];

        return $map;
    }

    /**
     * Возвращает карту статусов
     *
     * @return array
     */
    private function _getStatusMap(): array
    {
        $map = [
            self::STATUS_NEW => [self::STATUS_IN_PROGRESS, self::STATUS_CANCEL, self::STATUS_EXPIRED],
            self::STATUS_IN_PROGRESS => [self::STATUS_CANCEL, self::STATUS_COMPLETE, self::STATUS_EXPIRED],
            self::STATUS_EXPIRED => [self::STATUS_CANCEL]
        ];

        return $map;
    }


    /**
     * Возвращает действия, доступные для каждой роли
     *
     * @return array
     */
    private function _roleAllowedActions()
    {
        $map = [
            self::ROLE_PERFORMER => [ResponseAction::class, DenyAction::class],
            self::ROLE_CLIENT => [CancelAction::class, CompleteAction::class]
        ];

        return $map;
    }


    /**
     * Устанавливает дедлайн задания
     *
     * @param DateTime $date Объект даты
     * 
     * @return void
     */
    public function setFinishDate(DateTime $date): void
    {
        $currentDate = new DateTime();

        if ($date > $currentDate) {
            $this->_finishDate = $date;
        }
    }


    /**
     * Проверка роли
     * 
     * @param string $role Роль пользователя
     * 
     * @return void
     */
    private function checkRole(string $role): void
    {
        $availableRoles = [self::ROLE_PERFORMER, self::ROLE_CLIENT];

        if (!in_array($role, $availableRoles)) {
            throw new StatusActionException("Указана несуществующая роль: $role");
        }
    }
}
