<?php

namespace App\Config;

use App\Entity\HeaderLink;
use App\Entity\TaskStatus;

class HeaderLinkConfig
{
    private const TITLE_FIELD = 'title';
    private const ROUTE_FIELD = 'route';
    private const SUB_LINKS_FIELD = 'subLinks';
    private const ROUTE_PARAMS_FIELD = 'routeParams';
    private const HAS_PARENT_LINK_FIELD = 'hasParentLink';

    private const LOGIN_LINK_ID = 1;
    private const REGISTER_LINK_ID = 2;
    private const LOGOUT_LINK_ID = 3;

    private const TASKS_LINK_ID = 4;
    private const REMINDERS_LINK_ID = 5;
    private const ALL_TASKS_LINK_ID = 6;
    private const TODO_TASKS_LINK_ID = 7;
    private const FROZEN_TASKS_LINK_ID = 8;
    private const PROGRESS_LINK_ID = 9;
    private const POTENTIAL_TASKS_LINK_ID = 10;
    private const CANCELLED_TASKS_LINK_ID = 11;
    private const PENDING_TASKS_LINK_ID = 12;
    private const COMPLETED_TASKS_LINK_ID = 13;

    private const LINKS = [
        self::LOGIN_LINK_ID => [
            self::TITLE_FIELD => 'Login',
            self::ROUTE_FIELD => 'app_login'
        ],
        self::REGISTER_LINK_ID => [
            self::TITLE_FIELD => 'Register',
            self::ROUTE_FIELD => 'app_register'
        ],
        self::LOGOUT_LINK_ID => [
            self::TITLE_FIELD => 'Logout',
            self::ROUTE_FIELD => 'app_logout'
        ],
        self::TASKS_LINK_ID => [
            self::TITLE_FIELD => 'Tasks',
            self::ROUTE_FIELD => 'app_task_index',
            self::SUB_LINKS_FIELD => [
                self::PROGRESS_LINK_ID,
                self::FROZEN_TASKS_LINK_ID,
                self::POTENTIAL_TASKS_LINK_ID,
                self::CANCELLED_TASKS_LINK_ID,
                self::COMPLETED_TASKS_LINK_ID,
                self::ALL_TASKS_LINK_ID,
            ]
        ],
        self::TODO_TASKS_LINK_ID => [
            self::TITLE_FIELD => 'Todo',
            self::ROUTE_FIELD => 'app_task_todo'
        ],
        self::REMINDERS_LINK_ID => [
            self::TITLE_FIELD => 'Reminders',
            self::ROUTE_FIELD => 'app_task_reminders',
            self::HAS_PARENT_LINK_FIELD => false
        ],
        self::PROGRESS_LINK_ID => [
            self::TITLE_FIELD => 'In Progress',
            self::ROUTE_FIELD => 'app_task_status',
            self::HAS_PARENT_LINK_FIELD => false,
            self::ROUTE_PARAMS_FIELD => [
                'status' => TaskStatusConfig::PROGRESS_STATUS_SLUG
            ]
        ],
        self::FROZEN_TASKS_LINK_ID => [
            self::TITLE_FIELD => 'Frozen',
            self::ROUTE_FIELD => 'app_task_status',
            self::ROUTE_PARAMS_FIELD => [
                'status' => TaskStatusConfig::FROZEN_STATUS_SLUG
            ]
        ],
        self::POTENTIAL_TASKS_LINK_ID => [
            self::TITLE_FIELD => 'Potential',
            self::ROUTE_FIELD => 'app_task_status',
            self::ROUTE_PARAMS_FIELD => [
                'status' => TaskStatusConfig::POTENTIAL_STATUS_SLUG
            ]
        ],
        self::CANCELLED_TASKS_LINK_ID => [
            self::TITLE_FIELD => 'Cancelled',
            self::ROUTE_FIELD => 'app_task_status',
            self::ROUTE_PARAMS_FIELD => [
                'status' => TaskStatusConfig::CANCELLED_STATUS_SLUG
            ]
        ],
        self::PENDING_TASKS_LINK_ID => [
            self::TITLE_FIELD => 'Pending',
            self::ROUTE_FIELD => 'app_task_status',
            self::ROUTE_PARAMS_FIELD => [
                'status' => TaskStatusConfig::PENDING_STATUS_SLUG
            ]
        ],
        self::COMPLETED_TASKS_LINK_ID => [
            self::TITLE_FIELD => 'Completed',
            self::ROUTE_FIELD => 'app_task_status',
            self::ROUTE_PARAMS_FIELD => [
                'status' => TaskStatusConfig::COMPLETED_STATUS_SLUG
            ]
        ],
        self::ALL_TASKS_LINK_ID => [
            self::TITLE_FIELD => 'All',
            self::ROUTE_FIELD => 'app_task_index'
        ]
    ];

    private const USER_LINKS = [
        self::TASKS_LINK_ID,
        self::TODO_TASKS_LINK_ID,
        self::REMINDERS_LINK_ID,
        self::LOGOUT_LINK_ID
    ];

    private const ANONYMOUS_LINKS = [
        self::LOGIN_LINK_ID,
        self::REGISTER_LINK_ID
    ];

    private const TASK_LINK_STATUS_RELATION = [
        TaskStatusConfig::FROZEN_STATUS_ID => self::FROZEN_TASKS_LINK_ID,
        TaskStatusConfig::IN_PROGRESS_STATUS_ID => self::PROGRESS_LINK_ID,
        TaskStatusConfig::POTENTIAL_STATUS_ID => self::POTENTIAL_TASKS_LINK_ID,
        TaskStatusConfig::CANCELLED_STATUS_ID => self::CANCELLED_TASKS_LINK_ID,
        TaskStatusConfig::PENDING_STATUS_ID => self::PENDING_TASKS_LINK_ID,
        TaskStatusConfig::COMPLETED_STATUS_ID => self::COMPLETED_TASKS_LINK_ID
    ];

    public const DEFAULT_HAS_PARENT_LINK_VALUE = true;

    /**
     * @return HeaderLink[]
     */
    public function getUserLinks(): array
    {
        return $this->getLinksByIds(self::USER_LINKS);
    }

    /**
     * @return HeaderLink[]
     */
    public function getAnonymousLinks(): array
    {
        return $this->getLinksByIds(self::ANONYMOUS_LINKS);
    }

    /**
     * @param int[] $ids
     * @return HeaderLink[]
     */
    private function getLinksByIds(array $ids): array
    {
        $links = [];
        foreach ($ids as $id) {
            $links[] = $this->createLinkById($id);
        }
        return $links;
    }

    /**
     * @param int $id
     * @param array $rawLink
     * @return HeaderLink
     */
    private function createLinkById(int $id): HeaderLink
    {
        $rawLink = self::LINKS[$id];
        $subLinks = [];
        if (array_key_exists(self::SUB_LINKS_FIELD, $rawLink)) {
            foreach ($rawLink[self::SUB_LINKS_FIELD] as $subLinkId) {
                $subLinks[] = $this->createLinkById($subLinkId);
            }
        }
        $hasRouteParams = array_key_exists(self::ROUTE_PARAMS_FIELD, $rawLink);
        $routeParams = $hasRouteParams ? $rawLink[self::ROUTE_PARAMS_FIELD] : [];
        $hasParentLink = $rawLink[self::HAS_PARENT_LINK_FIELD] ?? self::DEFAULT_HAS_PARENT_LINK_VALUE;
        return new HeaderLink(
            $id,
            $rawLink[self::TITLE_FIELD],
            $rawLink[self::ROUTE_FIELD],
            $routeParams,
            $subLinks,
            $hasParentLink
        );
    }

    /**
     * @param int $id
     * @return HeaderLink
     */
    public function getLinkById(int $id): HeaderLink
    {
        return $this->createLinkById($id);
    }

    /**
     * @return HeaderLink
     */
    public function getAllTasksLink(): HeaderLink
    {
        return $this->getLinkById(self::ALL_TASKS_LINK_ID);
    }

    /**
     * @return HeaderLink
     */
    public function getTodoLink(): HeaderLink
    {
        return $this->getLinkById(self::TODO_TASKS_LINK_ID);
    }

    /**
     * @return HeaderLink
     */
    public function getRemindersLink(): HeaderLink
    {
        return $this->getLinkById(self::REMINDERS_LINK_ID);
    }

    /**
     * @param TaskStatus $taskStatus
     * @return HeaderLink
     */
    public function getLinkByTaskStatus(TaskStatus $taskStatus): HeaderLink
    {
        return $this->getLinkById(self::TASK_LINK_STATUS_RELATION[$taskStatus->getId()]);
    }

    /**
     * @param $linkId
     * @return bool
     */
    public function isHeaderLinkIdExists($linkId): bool
    {
        return array_key_exists($linkId, self::LINKS);
    }
}
