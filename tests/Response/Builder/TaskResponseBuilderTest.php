<?php

namespace App\Response\Builder;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskResponseBuilderTest extends TestCase
{
    private function getBuilder(): TaskResponseBuilder
    {
        return new TaskResponseBuilder();
    }

    public function testEmptyResponseBuild(): void
    {
        self::assertEquals(new JsonResponse([]), $this->getBuilder()->buildListResponse([]));
    }

    public function testBuild(): void
    {
        self::assertEquals(new JsonResponse([
            [
                'id' => 1,
                'title' => 'Root task',
                'parent' => null,
                'link' => 'root task link'
            ],
            [
                'id' => 2,
                'title' => 'First task',
                'parent' => 1,
                'link' => 'first task link'
            ]
        ]), $this->getBuilder()->buildListResponse($this->prepareTaskListMock()));
    }

    /**
     * @return Task[]
     */
    private function prepareTaskListMock(): iterable
    {
        $root = $this->createMock(Task::class);
        $root->method('getId')->willReturn(1);
        $root->method('getTitle')->willReturn('Root task');
        $root->method('getParent')->willReturn(null);
        $root->method('getLink')->willReturn('root task link');
        $tasks[] = $root;

        $task = $this->createMock(Task::class);
        $task->method('getId')->willReturn(2);
        $task->method('getTitle')->willReturn('First task');
        $task->method('getParent')->willReturn($root);
        $task->method('getLink')->willReturn('first task link');
        $tasks[] = $task;

        return $tasks;
    }
}
