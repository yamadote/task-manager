<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Exception\InvalidArgumentException;
use Gedmo\Tool\Wrapper\EntityWrapper;
use Gedmo\Tree\TreeListener;

/**
 * Class AbstractNesterTreeRepository
 * @package App\Repository
 * Copied from Gedmo\Tree\Entity\Repository\NestedTreeRepository
 */
abstract class NestedTreeRepository extends ServiceEntityRepository
{
    private TreeListener $treeListener;

    public function __construct(
        ManagerRegistry $registry,
        string $entityClass,
        TreeListener $treeListener
    ) {
        parent::__construct($registry, $entityClass);
        $this->treeListener = $treeListener;
    }

    /**
     * @throws MappingException
     */
    public function findChildren(
        $node = null,
        $direct = false,
        $sortByField = null,
        $direction = 'ASC',
        $includeNode = false
    ): array {
        return $this->getChildrenQueryBuilder($node, $direct, $sortByField, $direction, $includeNode)
            ->getQuery()
            ->getResult();
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder();
    }

    /**
     * @throws MappingException
     */
    protected function getChildrenQueryBuilder(
        $node = null,
        $direct = false,
        $sortByField = null,
        $direction = 'ASC',
        $includeNode = false
    ): QueryBuilder {
        $meta = $this->getClassMetadata();
        $config = $this->treeListener->getConfiguration($this->_em, $meta->name);

        $qb = $this->getQueryBuilder();
        $qb->select('node')
            ->from($config['useObjectClass'], 'node')
        ;
        if (null !== $node) {
            if ($node instanceof $meta->name) {
                $wrapped = new EntityWrapper($node, $this->_em);
                if (!$wrapped->hasValidIdentifier()) {
                    throw new InvalidArgumentException('Node is not managed by UnitOfWork');
                }
                if ($direct) {
                    $qb->where($qb->expr()->eq('node.'.$config['parent'], ':pid'));
                    $qb->setParameter('pid', $wrapped->getIdentifier());
                } else {
                    $left = $wrapped->getPropertyValue($config['left']);
                    $right = $wrapped->getPropertyValue($config['right']);
                    if ($left && $right) {
                        $qb->where($qb->expr()->lt('node.'.$config['right'], $right));
                        $qb->andWhere($qb->expr()->gt('node.'.$config['left'], $left));
                    }
                }
                if (isset($config['root'])) {
                    $qb->andWhere($qb->expr()->eq('node.'.$config['root'], ':rid'));
                    $qb->setParameter('rid', $wrapped->getPropertyValue($config['root']));
                }
                if ($includeNode) {
                    $idField = $meta->getSingleIdentifierFieldName();
                    $qb->where('('.$qb->getDqlPart('where').') OR node.'.$idField.' = :rootNode');
                    $qb->setParameter('rootNode', $node);
                }
            } else {
                throw new \InvalidArgumentException('Node is not related to this repository');
            }
        } else if ($direct) {
            $qb->where($qb->expr()->isNull('node.'.$config['parent']));
        }
        if (!$sortByField) {
            $qb->orderBy('node.'.$config['left'], 'ASC');
        } elseif (is_array($sortByField)) {
            $fields = '';
            foreach ($sortByField as $field) {
                $fields .= 'node.'.$field.',';
            }
            $fields = rtrim($fields, ',');
            $qb->orderBy($fields, $direction);
        } else if ($meta->hasField($sortByField) && in_array(strtolower($direction), ['asc', 'desc'])) {
            $qb->orderBy('node.'.$sortByField, $direction);
        } else {
            $message = "Invalid sort options specified: field - $sortByField, direction - $direction";
            throw new InvalidArgumentException($message);
        }

        return $qb;
    }

    /**
     * Get the Tree path query builder by given $node
     * @throws InvalidArgumentException - if input is not valid
     */
    protected function getPathQueryBuilder(object $node): QueryBuilder
    {
        $meta = $this->getClassMetadata();
        if (!$node instanceof $meta->name) {
            throw new InvalidArgumentException('Node is not related to this repository');
        }
        $config = $this->treeListener->getConfiguration($this->_em, $meta->name);
        $wrapped = new EntityWrapper($node, $this->_em);
        if (!$wrapped->hasValidIdentifier()) {
            throw new InvalidArgumentException('Node is not managed by UnitOfWork');
        }
        $left = $wrapped->getPropertyValue($config['left']);
        $right = $wrapped->getPropertyValue($config['right']);
        $qb = $this->getQueryBuilder();
        $qb->select('node')
            ->from($config['useObjectClass'], 'node')
            ->where($qb->expr()->lte('node.'.$config['left'], $left))
            ->andWhere($qb->expr()->gte('node.'.$config['right'], $right))
            ->orderBy('node.'.$config['left'], 'ASC')
        ;
        if (isset($config['root'])) {
            $rootId = $wrapped->getPropertyValue($config['root']);
            $qb->andWhere($qb->expr()->eq('node.'.$config['root'], ':rid'));
            $qb->setParameter('rid', $rootId);
        }

        return $qb;
    }

    /**
     * Get the Tree path of Nodes by given $node
     * returns list of Nodes in path
     */
    public function getPath(object $node): array
    {
        return $this->getPathQueryBuilder($node)->getQuery()->getResult();
    }
}
