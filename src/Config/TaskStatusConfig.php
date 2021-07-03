<?php

namespace App\Config;

use App\Collection\TaskStatusCollection;
use App\Entity\TaskStatus;

class TaskStatusConfig
{
    private const TITLE_FIELD = 'title';
    private const SLUG_FIELD = 'slug';
    private const COLOR_FIELD = 'color';

    public const NONE_STATUS_ID = 0;
    public const PENDING_STATUS_ID = 1;
    public const IN_PROGRESS_STATUS_ID = 2;
    public const FROZEN_STATUS_ID = 3;
    public const COMPLETED_STATUS_ID = 4;
    public const POTENTIAL_STATUS_ID = 5;
    public const CANCELLED_STATUS_ID = 6;

    public const IN_PROGRESS_STATUS_SLUG = 'progress';
    public const FROZEN_STATUS_SLUG = 'frozen';
    public const POTENTIAL_STATUS_SLUG = 'potential';
    public const CANCELLED_STATUS_SLUG = 'cancelled';
    public const PENDING_STATUS_SLUG = 'pending';
    public const COMPLETED_STATUS_SLUG = 'completed';

    /* This list order is used for displaying */
    private const STATUSES = [
        self::PENDING_STATUS_ID => [
            self::TITLE_FIELD => 'Pending',
            self::SLUG_FIELD => self::PENDING_STATUS_SLUG,
            self::COLOR_FIELD => '#87AFFA'
        ],
        self::IN_PROGRESS_STATUS_ID => [
            self::TITLE_FIELD => 'In Progress',
            self::SLUG_FIELD => self::IN_PROGRESS_STATUS_SLUG,
            self::COLOR_FIELD => '#FFA900'
        ],
        self::FROZEN_STATUS_ID => [
            self::TITLE_FIELD => 'Frozen',
            self::SLUG_FIELD => self::FROZEN_STATUS_SLUG,
            self::COLOR_FIELD => '#0099CC'
        ],
        self::COMPLETED_STATUS_ID => [
            self::TITLE_FIELD => 'Completed',
            self::SLUG_FIELD => self::COMPLETED_STATUS_SLUG,
            self::COLOR_FIELD => '#8FBC8F'
        ],
        self::POTENTIAL_STATUS_ID => [
            self::TITLE_FIELD => 'Potential',
            self::SLUG_FIELD => self::POTENTIAL_STATUS_SLUG,
            self::COLOR_FIELD => '#cc66ff'
        ],
        self::CANCELLED_STATUS_ID => [
            self::TITLE_FIELD => 'Cancelled',
            self::SLUG_FIELD => self::CANCELLED_STATUS_SLUG,
            self::COLOR_FIELD => '#778899'
        ],
        self::NONE_STATUS_ID => [
            self::TITLE_FIELD => 'None',
            self::SLUG_FIELD => 'none',
            self::COLOR_FIELD => 'transparent'
        ]
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

    public function getStatusCollection(): TaskStatusCollection
    {
        return $this->createTaskStatusCollection(array_keys(self::STATUSES));
    }

    public function getTodoStatusCollection(): TaskStatusCollection
    {
        return $this->createTaskStatusCollection([
            self::IN_PROGRESS_STATUS_ID,
            self::PENDING_STATUS_ID
        ]);
    }

    private function getStatusIdBySlug(string $statusSlug): string
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

    public function getStatusBySlug(string $statusSlug): TaskStatus
    {
        return $this->getStatusById($this->getStatusIdBySlug($statusSlug));
    }

    public function isStatusSlugExisting($slug): bool
    {
        foreach (self::STATUSES as $status) {
            if ($status[self::SLUG_FIELD] === $slug) {
                return true;
            }
        }
        return false;
    }

    public function getStatusById(int $id): TaskStatus
    {
        return $this->createTaskStatus($id, self::STATUSES[$id]);
    }

    private function createTaskStatusCollection(array $ids): TaskStatusCollection
    {
        $list = [];
        foreach ($ids as $id) {
            $list[$id] = $this->createTaskStatus($id, self::STATUSES[$id]);
        }
        return new TaskStatusCollection($list);
    }

    private function createTaskStatus(int $id, array $raw): TaskStatus
    {
        return new TaskStatus(
            $id,
            $raw[self::TITLE_FIELD],
            $raw[self::SLUG_FIELD],
            $raw[self::COLOR_FIELD]
        );
    }
}
