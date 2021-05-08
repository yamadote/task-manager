<?php

namespace App\Config;

use App\Entity\UserStatus;

class UserStatusConfig
{
    private const TITLE_FIELD = 'title';
    private const SLUG_FIELD = 'slug';

    private const NONE_STATUS_ID = 0;
    private const PENDING_STATUS_ID = 1;
    private const IN_PROGRESS_STATUS_ID = 2;
    private const FROZEN_STATUS_ID = 3;
    private const COMPLETED_STATUS_ID = 4;
    private const POTENTIAL_STATUS_ID = 5;
    private const CANCELED_STATUS_ID = 6;
    private const REMOVED_STATUS_ID = 7;

    /* This list order is used for displaying */
    private const STATUSES = [
        self::PENDING_STATUS_ID => [
            self::TITLE_FIELD => 'Pending',
            self::SLUG_FIELD => 'pending'
        ],
        self::IN_PROGRESS_STATUS_ID => [
            self::TITLE_FIELD => 'In Progress',
            self::SLUG_FIELD => 'progress'
        ],
        self::FROZEN_STATUS_ID => [
            self::TITLE_FIELD => 'Frozen',
            self::SLUG_FIELD => 'frozen'
        ],
        self::COMPLETED_STATUS_ID => [
            self::TITLE_FIELD => 'Completed',
            self::SLUG_FIELD => 'completed'
        ],
        self::POTENTIAL_STATUS_ID => [
            self::TITLE_FIELD => 'Potential',
            self::SLUG_FIELD => 'potential'
        ],
        self::CANCELED_STATUS_ID => [
            self::TITLE_FIELD => 'Canceled',
            self::SLUG_FIELD => 'canceled'
        ],
        self::NONE_STATUS_ID => [
            self::TITLE_FIELD => 'None',
            self::SLUG_FIELD => 'none'
        ],
        self::REMOVED_STATUS_ID => [
            self::TITLE_FIELD => 'Removed',
            self::SLUG_FIELD => 'removed'
        ],
    ];

    /**
     * @return string[]
     */
    public function getStatusTitles(): array
    {
        return array_column(self::STATUSES, self::TITLE_FIELD);
    }

    /**
     * @return UserStatus[]
     */
    public function getStatusList(): array
    {
        $list = [];
        foreach (self::STATUSES as $id => $raw) {
            $list[] = $this->createStatusEntity($id, $raw);
        }
        return $list;
    }

    private function createStatusEntity(int $id, array $raw): UserStatus
    {
        return new UserStatus(
            $id,
            $raw[self::TITLE_FIELD],
            $raw[self::SLUG_FIELD]
        );
    }

    public function getRemovedStatusId(): int
    {
        return self::REMOVED_STATUS_ID;
    }
}
