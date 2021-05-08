<?php

namespace App\HeaderLink\Repository;

use App\HeaderLink\Dto\HeaderLinkDto;

class HeaderLinkRepository
{
    private const TITLE_FIELD = 'title';
    private const ROUTE = 'route';

    private const INDEX_LINK_ID = 0;
    private const LOGIN_LINK_ID = 1;
    private const REGISTER_LINK_ID = 2;
    private const LOGOUT_LINK_ID = 3;
    private const TASKS_LINK_ID = 4;

    private const LINKS = [
        self::INDEX_LINK_ID => [
            self::TITLE_FIELD => 'Index',
            self::ROUTE => 'app_index'
        ],
        self::LOGIN_LINK_ID => [
            self::TITLE_FIELD => 'Login',
            self::ROUTE => 'app_login'
        ],
        self::REGISTER_LINK_ID => [
            self::TITLE_FIELD => 'Register',
            self::ROUTE => 'app_register'
        ],
        self::LOGOUT_LINK_ID => [
            self::TITLE_FIELD => 'Logout',
            self::ROUTE => 'app_logout'
        ],
        self::TASKS_LINK_ID => [
            self::TITLE_FIELD => 'Tasks',
            self::ROUTE => 'app_task_index'
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
     * @return HeaderLinkDto[]
     */
    public function getUserLinks(): array
    {
        return $this->getLinksByIds(self::USER_LINKS);
    }

    /**
     * @return HeaderLinkDto[]
     */
    public function getAnonymousLinks(): array
    {
        return $this->getLinksByIds(self::ANONYMOUS_LINKS);
    }

    /**
     * @param int[] $ids
     * @return HeaderLinkDto[]
     */
    private function getLinksByIds(array $ids): array
    {
        $links = [];
        foreach ($ids as $id) {
            $links[] = $this->createLink(self::LINKS[$id]);
        }
        return $links;
    }

    private function createLink(array $raw): HeaderLinkDto
    {
        return new HeaderLinkDto(
            $raw[self::TITLE_FIELD],
            $raw[self::ROUTE]
        );
    }
}
