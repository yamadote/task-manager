<?php

namespace App\Config;

use App\Entity\HeaderLink;

class HeaderLinkConfig
{
    private const TITLE_FIELD = 'title';
    private const ROUTE_FIELD = 'route';
    private const SUB_LINKS_FIELD = 'subLinks';
    private const ROUTE_PARAMS_FIELD = 'routeParams';

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
                self::TODO_TASKS_LINK_ID => [
                    self::TITLE_FIELD => 'Todo',
                    self::ROUTE_FIELD => 'app_task_todo'
                ],
                self::REMINDERS_LINK_ID => [
                    self::TITLE_FIELD => 'Reminders',
                    self::ROUTE_FIELD => 'app_task_reminders'
                ],
                self::PROGRESS_LINK_ID => [
                    self::TITLE_FIELD => 'In Progress',
                    self::ROUTE_FIELD => 'app_task_index',
                    self::ROUTE_PARAMS_FIELD => [
                        'status' => UserStatusConfig::PROGRESS_STATUS_SLUG
                    ]
                ],
                self::FROZEN_TASKS_LINK_ID => [
                    self::TITLE_FIELD => 'Frozen',
                    self::ROUTE_FIELD => 'app_task_index',
                    self::ROUTE_PARAMS_FIELD => [
                        'status' => UserStatusConfig::FROZEN_STATUS_SLUG
                    ]
                ],
                self::POTENTIAL_TASKS_LINK_ID => [
                    self::TITLE_FIELD => 'Potential',
                    self::ROUTE_FIELD => 'app_task_index',
                    self::ROUTE_PARAMS_FIELD => [
                        'status' => UserStatusConfig::POTENTIAL_STATUS_SLUG
                    ]
                ],
                self::CANCELLED_TASKS_LINK_ID => [
                    self::TITLE_FIELD => 'Cancelled',
                    self::ROUTE_FIELD => 'app_task_index',
                    self::ROUTE_PARAMS_FIELD => [
                        'status' => UserStatusConfig::CANCELLED_STATUS_SLUG
                    ]
                ],
                self::ALL_TASKS_LINK_ID => [
                    self::TITLE_FIELD => 'All',
                    self::ROUTE_FIELD => 'app_task_index'
                ]
            ]
        ]
    ];

    private const USER_LINKS = [
        self::TASKS_LINK_ID,
        self::LOGOUT_LINK_ID
    ];

    private const ANONYMOUS_LINKS = [
        self::LOGIN_LINK_ID,
        self::REGISTER_LINK_ID
    ];

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
            $links[] = $this->createLink($id, self::LINKS[$id]);
        }
        return $links;
    }

    private function createLink(int $id, array $rawLink): HeaderLink
    {
        $subLinks = [];
        if (array_key_exists(self::SUB_LINKS_FIELD, $rawLink)) {
            foreach ($rawLink[self::SUB_LINKS_FIELD] as $subLinkId => $rawSubLink) {
                $subLinks[] = $this->createLink($subLinkId, $rawSubLink);
            }
        }
        $hasRouteParams = array_key_exists(self::ROUTE_PARAMS_FIELD, $rawLink);
        $routeParams = $hasRouteParams ? $rawLink[self::ROUTE_PARAMS_FIELD] : [];
        return new HeaderLink(
            $id,
            $rawLink[self::TITLE_FIELD],
            $rawLink[self::ROUTE_FIELD],
            $routeParams,
            $subLinks
        );
    }
}
