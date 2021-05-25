<?php

namespace App\Repository;

use App\Entity\TaskTitleEditLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaskTitleEditLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskTitleEditLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskTitleEditLog[]    findAll()
 * @method TaskTitleEditLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskTitleEditLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskTitleEditLog::class);
    }
}
