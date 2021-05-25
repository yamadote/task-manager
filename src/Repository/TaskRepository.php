<?php

namespace App\Repository;

use App\Builder\TaskBuilder;
use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Tree\TreeListener;
use RuntimeException;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Task[]    findChildren(Task $node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
 */
class TaskRepository extends NestedTreeRepository
{
    /** @var TaskStatusConfig */
    private $taskStatusConfig;

    /** @var TaskBuilder */
    private $taskBuilder;

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

    /**
     * @param User $user
     * @return QueryBuilder
     */
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
     * @param User $user
     * @param Task|null $parent
     * @return Task[]
     */
    public function findUserTasks(User $user): array
    {
        $queryBuilder = $this->prepareUserTasksQueryBuilder($user);
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param User $getUser
     * @return Task[]
     */
    public function findUserReminders(User $user): array
    {
        $queryBuilder = $this->prepareUserTasksQueryBuilder($user);
        $queryBuilder->andWhere("t.reminder < :time");
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param User $user
     * @param Task|null $parent
     * @param int[] $statusList
     * @return Task[]
     */
    public function findUserTasksHierarchyByStatusList(User $user, array $statusList): array
    {
        $queryBuilder = $this->prepareUserTasksQueryBuilder($user);
        $queryBuilder->distinct();
        $queryBuilder->join(Task::class, 'c', 'WITH', 'c.user = :user');
        $queryBuilder->setParameter('user', $user);
        $where = "t.status IN (:statusList) OR (c.status IN (:statusList) AND t.lft < c.lft AND c.rgt < t.rgt)";
        $queryBuilder->andWhere($where);
        $queryBuilder->setParameter('statusList', $statusList);
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param User $user
     * @param Task|null $parent
     * @param int $status
     * @return Task[]
     */
    public function findUserTasksHierarchyByStatus(User $user, int $status): array
    {
        return $this->findUserTasksHierarchyByStatusList($user, [$status]);
    }

    /**
     * @param User $user
     * @return Task
     */
    public function findUserRootTask(User $user): Task
    {
        $root = $this->findOneBy(['user' => $user, 'parent' => null]);
        return $root ?? $this->createRootTask($user);
    }

    /**
     * @param User $user
     * @return Task
     */
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
}
