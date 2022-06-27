<?php

namespace App\Repository;

use App\Collection\HistoryActionCollection;
use App\Entity\HistoryAction;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HistoryAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoryAction[]    findAll()
 * @method HistoryAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoryAction::class);
    }

    public function findByUser(User $user): HistoryActionCollection
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder->andWhere('a.user = :user');
        $queryBuilder->setParameter('user', $user);
        $queryBuilder->orderBy('a.id', 'DESC');
//        $queryBuilder->setMaxResults(30);
        return new HistoryActionCollection($queryBuilder->getQuery()->getResult());
    }

    public function findByTask(Task $task): HistoryActionCollection
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder->andWhere('a.task = :task');
        $queryBuilder->setParameter('task', $task);
        $queryBuilder->orderBy('a.id', 'DESC');
//        $queryBuilder->setMaxResults(30);
        return new HistoryActionCollection($queryBuilder->getQuery()->getResult());
    }
}
