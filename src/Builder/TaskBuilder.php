<?php

namespace App\Builder;

use App\Config\TaskConfig;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class TaskBuilder
{
    private const STATUS_REQUEST_FIELD = 'status';

    /** @var TaskConfig */
    private $taskConfig;

    /** @var TaskStatusConfig */
    private $taskStatusConfig;

    public function __construct(
        TaskConfig $taskConfig,
        TaskStatusConfig $taskStatusConfig
    ) {
        $this->taskConfig = $taskConfig;
        $this->taskStatusConfig = $taskStatusConfig;
    }

    /**
     * @param Request $request
     * @param User $user
     * @param Task|null $parent
     * @return Task
     */
    public function buildFromRequest(Request $request, User $user, ?Task $parent): Task
    {
        $task = new Task();
        $task->setUser($user);
        $task->setTitle($this->taskConfig->getNewTaskTitle());
        $task->setStatus($this->getStatusOrDefault($request));
        if (null !== $parent) {
            $task->setParent($parent);
        }
        return $task;
    }

    /**
     * @param Request $request
     * @return string
     */
    private function getStatusOrDefault(Request $request): string
    {
        if (!$request->query->has(self::STATUS_REQUEST_FIELD)) {
            return $this->taskConfig->getNewTaskDefaultStatus();
        }
        $slug = $request->query->get(self::STATUS_REQUEST_FIELD);
        if (!$this->taskStatusConfig->isStatusSlugExisting($slug)) {
            return $this->taskConfig->getNewTaskDefaultStatus();
        }
        return $this->taskStatusConfig->getStatusBySlug($slug)->getId();
    }

    /**
     * @param User $user
     * @return Task
     */
    public function buildRootTask(User $user): Task
    {
        $root = new Task();
        $root->setUser($user);
        $root->setTitle($this->taskConfig->getRootTaskTitle());
        $root->setStatus($this->taskConfig->getRootTaskDefaultStatus());
        return $root;
    }
}
