<?php

namespace App\Repository;

use App\Config\UserStatusConfig;
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
    /** @var UserStatusConfig */
    private $userStatusConfig;

    public function __construct(ManagerRegistry $registry, UserStatusConfig $userStatusConfig)
    {
        parent::__construct($registry, Task::class);
        $this->userStatusConfig = $userStatusConfig;
    }

    private function prepareUserTasksQueryBuilder(User $user): QueryBuilder
    {
        $removedStatusId = $this->userStatusConfig->getRemovedStatusId();
        return $this->createQueryBuilder('t')
            ->andWhere("t.status <> :status")
            ->andWhere("t.user = :user")
            ->setParameters([
                'status' => $removedStatusId,
                'user' => $user,
                'time' => new DateTime()
            ])
            ->orderBy("CASE WHEN t.reminder < :time THEN 1 ELSE 0 END", "DESC")
            ->addOrderBy("t.status", "ASC")
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
}
