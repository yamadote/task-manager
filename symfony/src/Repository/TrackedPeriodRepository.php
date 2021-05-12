<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\TrackedPeriod;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrackedPeriod|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrackedPeriod|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrackedPeriod[]    findAll()
 * @method TrackedPeriod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackedPeriodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrackedPeriod::class);
    }

    /**
     * @param User $user
     * @return TrackedPeriod|null
     */
    public function getActivePeriod(User $user): ?TrackedPeriod
    {
        return $this->findOneBy(['user' => $user, 'finishedAt' => null]);
    }

    /**
     * @param User $getUser
     * @param Task $task
     * @return TrackedPeriod|null
     */
    public function findLastTrackedPeriod(User $user): ?TrackedPeriod
    {
        return $this->findOneBy(['user' => $user], ['id' => 'DESC']);
    }
}
