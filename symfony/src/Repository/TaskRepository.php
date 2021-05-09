<?php

namespace App\Repository;

use App\Config\TaskStatusConfig;
use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    /** @var TaskStatusConfig */
    private $taskStatusConfig;

    public function __construct(ManagerRegistry $registry, TaskStatusConfig $taskStatusConfig)
    {
        parent::__construct($registry, Task::class);
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
            ->setParameters([
                'user' => $user,
                'time' => new DateTime()
            ])
            ->orderBy("CASE WHEN t.reminder < :time THEN 1 ELSE 0 END", "DESC")
            ->addOrderBy($compiledStatusOrder, "ASC")
            ->addOrderBy("t.id", "DESC")
        ;
    }

    /**
     * @return Task[]
     */
    public function findUserTasks(User $user): array
    {
        return $this->prepareUserTasksQueryBuilder($user)
            ->getQuery()->getResult();
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
