<?php

namespace App\Repository;

use App\Config\UserStatusConfig;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    /**
     * @return Task[]
     */
    public function findUserTasks(User $user): array
    {
        $removedStatusId = $this->userStatusConfig->getRemovedStatusId();
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->andWhere("t.status <> :status AND t.user = :user");
        $queryBuilder->setParameters([
            'status' => $removedStatusId,
            'user' => $user
        ]);
        $queryBuilder->orderBy("t.id", "DESC");
        return $queryBuilder->getQuery()->getResult();
    }
}
