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
    private const CANCELLED_STATUS_ID = 6;
    private const REMOVED_STATUS_ID = 7;

    public const PROGRESS_STATUS_SLUG = 'progress';
    public const FROZEN_STATUS_SLUG = 'frozen';
    public const POTENTIAL_STATUS_SLUG = 'potential';
    public const CANCELLED_STATUS_SLUG = 'cancelled';

    /* This list order is used for displaying */
    private const STATUSES = [
        self::PENDING_STATUS_ID => [
            self::TITLE_FIELD => 'Pending',
            self::SLUG_FIELD => 'pending'
        ],
        self::IN_PROGRESS_STATUS_ID => [
            self::TITLE_FIELD => 'In Progress',
            self::SLUG_FIELD => self::PROGRESS_STATUS_SLUG
        ],
        self::FROZEN_STATUS_ID => [
            self::TITLE_FIELD => 'Frozen',
            self::SLUG_FIELD => self::FROZEN_STATUS_SLUG
        ],
        self::COMPLETED_STATUS_ID => [
            self::TITLE_FIELD => 'Completed',
            self::SLUG_FIELD => 'completed'
        ],
        self::POTENTIAL_STATUS_ID => [
            self::TITLE_FIELD => 'Potential',
            self::SLUG_FIELD => self::POTENTIAL_STATUS_SLUG
        ],
        self::CANCELLED_STATUS_ID => [
            self::TITLE_FIELD => 'Cancelled',
            self::SLUG_FIELD => self::CANCELLED_STATUS_SLUG
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

    private const TASKS_LIST_STATUS_ORDER = [
        self::NONE_STATUS_ID,
        self::IN_PROGRESS_STATUS_ID,
        self::PENDING_STATUS_ID,
        self::FROZEN_STATUS_ID,
        self::POTENTIAL_STATUS_ID,
        self::CANCELLED_STATUS_ID,
        self::COMPLETED_STATUS_ID
    ];

    /**
     * @return string[]
     */
    public function getStatusTitles(): array
    {
        $titles = [];
        foreach (self::STATUSES as $id => $raw) {
            $titles[$id] = $raw[self::TITLE_FIELD];
        }
        return $titles;
    }

    /**
     * @return UserStatus[]
     */
    public function getStatusList(): array
    {
        $list = [];
        foreach (self::STATUSES as $id => $raw) {
            $list[$id] = $this->createStatusEntity($id, $raw);
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

    /**
     * @return int[]
     */
    public function getTodoStatusIds(): array
    {
        return [
            self::IN_PROGRESS_STATUS_ID,
            self::PENDING_STATUS_ID
        ];
    }

    private function getStatusIdBySlug(string $statusSlug)
    {
        foreach (self::STATUSES as $id => $raw) {
            if ($raw[self::SLUG_FIELD] === $statusSlug) {
                return $id;
            }
        }
        return self::NONE_STATUS_ID;
    }

    public function getTasksListStatusOrder(): array
    {
        return self::TASKS_LIST_STATUS_ORDER;
    }

    public function getStatusBySlug(string $statusSlug): UserStatus
    {
        $id = $this->getStatusIdBySlug($statusSlug);
        return $this->createStatusEntity($id, self::STATUSES[$id]);
    }
}
