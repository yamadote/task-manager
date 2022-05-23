<?php

namespace App\Repository;

use App\Collection\ActionCollection;
use App\Entity\Action;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Action|null find($id, $lockMode = null, $lockVersion = null)
 * @method Action|null findOneBy(array $criteria, array $orderBy = null)
 * @method Action[]    findAll()
 * @method Action[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Action::class);
    }

    public function findByUser(User $user): ActionCollection
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder->andWhere('a.user = :user');
        $queryBuilder->setParameter('user', $user);
        $queryBuilder->orderBy('a.id', 'DESC');
//        $queryBuilder->setMaxResults(30);
        return new ActionCollection($queryBuilder->getQuery()->getResult());
    }

    public function findByTask(Task $task): ActionCollection
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder->andWhere('a.task = :task');
        $queryBuilder->setParameter('task', $task);
        $queryBuilder->orderBy('a.id', 'DESC');
//        $queryBuilder->setMaxResults(30);
        return new ActionCollection($queryBuilder->getQuery()->getResult());
    }
}
