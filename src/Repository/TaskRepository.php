<?php

namespace App\Repository;

use App\Builder\TaskBuilder;
use App\Collection\TaskCollection;
use App\Collection\TaskStatusCollection;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Entity\TaskStatus;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\ORMException;
use RuntimeException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Tree\TreeListener;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Task[]    findChildren(Task $node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
 */
class TaskRepository extends NestedTreeRepository
{
    private TaskStatusConfig $taskStatusConfig;
    private TaskBuilder $taskBuilder;

    public function __construct(
        ManagerRegistry $registry,
        TaskStatusConfig $taskStatusConfig,
        TreeListener $treeListener,
        TaskBuilder $taskBuilder
    ) {
        parent::__construct($registry, Task::class, $treeListener);
        $this->taskStatusConfig = $taskStatusConfig;
        $this->taskBuilder = $taskBuilder;
    }

    private function prepareUserTasksQueryBuilder(User $user): QueryBuilder
    {
        $statusOrder = $this->taskStatusConfig->getTasksListStatusOrder();
        $compiledStatusOrder = "CASE t.status ";
        foreach ($statusOrder as $order => $statusId) {
            $compiledStatusOrder .= " WHEN " . $statusId . " THEN " . $order;
        }
        $compiledStatusOrder .= " ELSE -1 END";

        return $this->createQueryBuilder('t')
            ->andWhere("t.user = :user")
            ->setParameters(['user' => $user, 'time' => new DateTime()])
            ->orderBy("CASE WHEN t.reminder < :time THEN 1 ELSE 0 END", "DESC")
            ->addOrderBy($compiledStatusOrder, "ASC")
            ->addOrderBy("t.id", "DESC")
        ;
    }

    public function findUserTasks(User $user): TaskCollection
    {
        $queryBuilder = $this->prepareUserTasksQueryBuilder($user);
        return new TaskCollection($queryBuilder->getQuery()->getResult());
    }

    public function findUserReminders(User $user): TaskCollection
    {
        $queryBuilder = $this->prepareUserTasksQueryBuilder($user);
        $queryBuilder->andWhere("t.reminder < :time");
        return new TaskCollection($queryBuilder->getQuery()->getResult());
    }

    public function findUserTasksByStatusList(
        User $user,
        TaskStatusCollection $taskStatusCollection,
        bool $fullHierarchy = false
    ): TaskCollection {
        $queryBuilder = $this->prepareUserTasksQueryBuilder($user);
        if ($fullHierarchy) {
            $queryBuilder->distinct();
            $queryBuilder->join(Task::class, 'c', 'WITH', 'c.user = :user');
            $queryBuilder->setParameter('user', $user);
            $where = "t.status IN (:statusList) OR (c.status IN (:statusList) AND t.lft < c.lft AND c.rgt < t.rgt)";
            $queryBuilder->andWhere($where);
        } else {
            $queryBuilder->andWhere("t.status IN (:statusList)");
        }
        $queryBuilder->setParameter('statusList', $taskStatusCollection->getIds());
        return new TaskCollection($queryBuilder->getQuery()->getResult());
    }

    public function findUserTasksByStatus(User $user, TaskStatus $status, bool $fullHierarchy = false): TaskCollection
    {
        return $this->findUserTasksByStatusList($user, new TaskStatusCollection([$status]), $fullHierarchy);
    }

    public function findUserRootTask(User $user): Task
    {
        $root = $this->findOneBy(['user' => $user, 'parent' => null]);
        return $root ?? $this->createRootTask($user);
    }

    private function createRootTask(User $user): Task
    {
        $root = $this->taskBuilder->buildRootTask($user);
        try {
            $this->_em->persist($root);
            $this->_em->flush();
        } catch (ORMException $e) {
            throw new RuntimeException("Something went wrong!", 0, $e);
        }
        return $root;
    }

    public function getTaskPath(Task $task): TaskCollection
    {
        $nodes = $this->getPath($task);
        return new TaskCollection($nodes);
    }

    public function increaseTrackedTime(Task $task, int $increase): void
    {
        $task->increaseTrackedTime($increase);
        $path = $this->getTaskPath($task);
        foreach ($path->getIterator() as $item) {
            if ($item->equals($task)) {
                continue;
            }
            $item->increaseChildrenTrackedTime($increase);
        }
    }
}
