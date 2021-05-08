<?php

namespace App\Config;

use App\Entity\HeaderLink;

class HeaderLinkConfig
{
    private const TITLE_FIELD = 'title';
    private const ROUTE_FIELD = 'route';

    private const INDEX_LINK_ID = 0;
    private const LOGIN_LINK_ID = 1;
    private const REGISTER_LINK_ID = 2;
    private const LOGOUT_LINK_ID = 3;
    private const TASKS_LINK_ID = 4;

    private const LINKS = [
        self::INDEX_LINK_ID => [
            self::TITLE_FIELD => 'Index',
            self::ROUTE_FIELD => 'app_index'
        ],
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
            self::ROUTE_FIELD => 'app_task_index'
        ]
    ];

    private const USER_LINKS = [
        self::INDEX_LINK_ID,
        self::TASKS_LINK_ID,
        self::LOGOUT_LINK_ID
    ];

    private const ANONYMOUS_LINKS = [
        self::INDEX_LINK_ID,
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

    private function createLink(int $id, array $raw): HeaderLink
    {
        return new HeaderLink(
            $id,
            $raw[self::TITLE_FIELD],
            $raw[self::ROUTE_FIELD]
        );
    }
}
