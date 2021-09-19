<?php

declare(strict_types=1);

namespace App\Repository\View;

use App\Entity\View\Wares;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Wares|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wares|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wares[]    findAll()
 * @method Wares[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WaresRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wares::class);
    }

    /**
     * @param string $storeId
     * @param array $filters
     * @param int $page
     * @param int $limit
     * @return array|Wares[]
     */
    public function search(string $storeId, array $filters, ?int $page = null, ?int $limit = null): array
    {
        $qb = $this->buildFilterQuery($storeId, $filters);

        if ($page !== null && $limit !== null) {
            $qb->setMaxResults($limit);
            $qb->setFirstResult(($page - 1) * $limit);
        }
        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $storeId
     * @param array $filters
     * @return int
     * @throws NoResultException|NonUniqueResultException
     */
    public function countBy(string $storeId, array $filters): int
    {
        $qb = $this->buildFilterQuery($storeId, $filters);
        $qb->select('COUNT(w.characteristic)');

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    /**
     * @param string $storeId
     * @param array $filters
     * @return QueryBuilder
     */
    private function buildFilterQuery(string $storeId, array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('w');

        $filter = $filters['filter'] ?? null;
        $sortBy = $filters['sortBy'] ?? null;
        $sortOrder = 'ASC';

        if (array_key_exists('descending', $filters)) {
            $sortOrder = $filters['descending'] === 'true' ? 'DESC' : 'ASC';
        }

        $qb->where($qb->expr()->eq('w.store', ':store'))
            ->setParameter('store', $storeId);

        if ($filter !== null) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('w.serial', ':filter'),
                    $qb->expr()->like('w.name', ':filter')
                )
            );
            $qb->setParameter('filter', '%' . $filter . '%');
        }

        if ($sortBy) {
            $qb->addOrderBy('w.' . $sortBy, $sortOrder);
        }

        return $qb;
    }
}
