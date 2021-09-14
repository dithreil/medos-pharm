<?php

declare(strict_types=1);

namespace App\Repository\Document;

use App\Entity\Document\Income;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnexpectedResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Income|null find($id, $lockMode = null, $lockVersion = null)
 * @method Income|null findOneBy(array $criteria, array $orderBy = null)
 * @method Income[]    findAll()
 * @method Income[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncomeRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Income::class);
    }

    /**
     * @param array $filters
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function search(array $filters, ?int $page = null, ?int $limit = null): array
    {
        $qb = $this->buildFilterQuery($filters);

        if ($page !== null && $limit !== null) {
            $qb->setMaxResults($limit);
            $qb->setFirstResult(($page - 1) * $limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $filters
     * @return int
     * @throws UnexpectedResultException
     */
    public function countBy(array $filters): int
    {
        $qb = $this->buildFilterQuery($filters);
        $qb->select('COUNT(i.id)');

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    /**
     * @param array $filters
     * @return QueryBuilder
     */
    private function buildFilterQuery(array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('i')
            ->innerJoin('i.store', 'st')
            ->innerJoin('i.supplier', 'su');

        $filter = $filters['filter'] ?? null;
        $sortBy = $filters['sortBy'] ?? null;
        $sortOrder = 'ASC';

        if (array_key_exists('descending', $filters)) {
            $sortOrder = $filters['descending'] === 'true' ? 'DESC' : 'ASC';
        }

        if ($filter !== null) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('st.name', ':filter'),
                    $qb->expr()->like('su.name', ':filter')
                )
            );
            $qb->setParameter('filter', '%' . $filter . '%');
        }

        if ($sortBy) {
            $qb->addOrderBy('i.' . $sortBy, $sortOrder);
        }

        return $qb;
    }
}
