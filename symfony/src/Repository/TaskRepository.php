<?php

namespace App\Repository;

use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Tree\TreeListener;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Task[]    children($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
 */
class TaskRepository extends NestedTreeRepository
{
    /** @var TaskStatusConfig */
    private $taskStatusConfig;

    public function __construct(
        ManagerRegistry $registry,
        TaskStatusConfig $taskStatusConfig,
        TreeListener $treeListener
    ) {
        parent::__construct($registry, Task::class, $treeListener);
        $this->taskStatusConfig = $taskStatusConfig;
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

    /**
     * @param QueryBuilder $queryBuilder
     * @param Task|null $parent
     * @return QueryBuilder
     */
    private function setParentFilter(QueryBuilder $queryBuilder, ?Task $parent): void
    {
        if (null === $parent) {
            $queryBuilder->andWhere("t.parent is null");
        } else {
            $queryBuilder->andWhere("t.parent = :parent");
            $queryBuilder->setParameter("parent", $parent);
        }
    }

    /**
     * @param User $user
     * @param Task|null $parent
     * @return Task[]
     */
    public function findUserTasks(User $user, ?Task $parent): array
    {
        $queryBuilder = $this->prepareUserTasksQueryBuilder($user);
        $this->setParentFilter($queryBuilder, $parent);
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param User $getUser
     * @return Task[]
     */
    public function findUserReminders(User $user): array
    {
        return $this->prepareUserTasksQueryBuilder($user)
            ->andWhere("t.reminder < :time")
            ->getQuery()->getResult();
    }

    public function findUserTodoTasks(User $user): array
    {
        $statusIds = $this->taskStatusConfig->getTodoStatusIds();
        return $this->prepareUserTasksQueryBuilder($user)
            ->andWhere("t.reminder < :time OR t.status in (" . implode(',', $statusIds). ")")
            ->getQuery()->getResult();
    }

    public function findUserTasksByStatus(User $user, int $status): array
    {
        return $this->prepareUserTasksQueryBuilder($user)
            ->andWhere("t.status = :status")
            ->setParameter('status', $status)
            ->getQuery()->getResult();
    }
}
