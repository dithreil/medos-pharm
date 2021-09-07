<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Nomenclature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnexpectedResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Nomenclature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nomenclature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Nomenclature[]    findAll()
 * @method Nomenclature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NomenclatureRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nomenclature::class);
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
        $qb->select('COUNT(n.id)');

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    /**
     * @param array $filters
     * @return QueryBuilder
     */
    private function buildFilterQuery(array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('n')
            ->innerJoin('n.producer', 'p');

        $filter = $filters['filter'] ?? null;
        $sortBy = $filters['sortBy'] ?? null;
        $sortOrder = 'ASC';

        if (array_key_exists('descending', $filters)) {
            $sortOrder = $filters['descending'] === 'true' ? 'DESC' : 'ASC';
        }

        if ($filter !== null) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('n.name', ':filter'),
                    $qb->expr()->like('p.shortName', ':filter')
                )
            );
            $qb->setParameter('filter', '%' . $filter . '%');
        }

        if ($sortBy) {
            $qb->addOrderBy('n.' . $sortBy, $sortOrder);
        }

        return $qb;
    }
}
