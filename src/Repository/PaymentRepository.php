<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Payment;
use App\Exception\AppException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\UnexpectedResultException;
use Doctrine\Persistence\ManagerRegistry;

class PaymentRepository extends ServiceEntityRepository
{
    /**
     * PaymentRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * @param \DateTimeImmutable $createTime
     * @param string[] $filters
     * @param int|null $page
     * @param int|null $limit
     * @return Payment[]
     */
    public function searchForAdmin(\DateTimeImmutable $createTime, array $filters, ?int $page = null, ?int $limit = null): array
    {
        $qb = $this->buildFilterQueryForAdmin($createTime, $filters);

        if ($page !== null && $limit !== null) {
            $qb->setMaxResults($limit);
            $qb->setFirstResult(($page - 1) * $limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $clientId
     * @param int|null $page
     * @param int|null $limit
     * @return Payment[]
     */
    public function search(string $clientId, ?int $page = null, ?int $limit = null): array
    {
        $qb = $this->buildFilterQuery($clientId);

        if ($page !== null && $limit !== null) {
            $qb->setMaxResults($limit);
            $qb->setFirstResult(($page - 1) * $limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \DateTimeImmutable $createTime
     * @param string[] $filters
     * @return int
     * @throws UnexpectedResultException|NonUniqueResultException
     */
    public function countByForAdmin(\DateTimeImmutable $createTime, $filters): int
    {
        $qb = $this->buildFilterQueryForAdmin($createTime, $filters);
        $qb->select('COUNT(p.id)');

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    /**
     * @param string $clientId
     * @return int
     * @throws UnexpectedResultException|NonUniqueResultException
     */
    public function countBy(string $clientId): int
    {
        $qb = $this->buildFilterQuery($clientId);
        $qb->select('COUNT(p.id)');

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    /**
     * @param \DateTimeImmutable $createTime
     * @param string[] $filters
     * @return QueryBuilder
     */
    private function buildFilterQueryForAdmin(\DateTimeImmutable $createTime, array $filters): QueryBuilder
    {
        $finalTime = $createTime->modify('+1 day');

        $filter = $filters['filter'] ?? null;
        $sortBy = $filters['sortBy'] ?? null;
        $sortOrder = 'ASC';

        if (array_key_exists('descending', $filters)) {
            $sortOrder = $filters['descending'] === 'true' ? 'DESC' : 'ASC';
        }

        $qb = $this->createQueryBuilder('p');
        $qb->innerJoin('p.order', 'o');
        $qb->innerJoin('o.client', 'c');
        $qb->innerJoin('o.employee', 'e');

        $qb->where($qb->expr()->between('p.createTime', ':createTime', ':finalTime'))
            ->setParameter('createTime', $createTime)
            ->setParameter('finalTime', $finalTime);

        if ($filter !== null) {
            $qb->innerJoin('o.client', 'c');
            $qb->innerJoin('o.employee', 'e');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like("CONCAT(c.lastName, ' ', c.firstName, ' ', c.patronymic)", ':filter'),
                    $qb->expr()->like('c.lastName', ':filter'),
                    $qb->expr()->like('c.firstName', ':filter'),
                    $qb->expr()->like('c.patronymic', ':filter'),
                    $qb->expr()->like("CONCAT(e.lastName, ' ', e.firstName, ' ', e.patronymic)", ':filter'),
                    $qb->expr()->like('e.lastName', ':filter'),
                    $qb->expr()->like('e.firstName', ':filter'),
                    $qb->expr()->like('e.patronymic', ':filter'),
                    $qb->expr()->like("DATE_FORMAT(o.paymentTime, '%d.%m.%Y')", ':filter'),
                    $qb->expr()->like("DATE_FORMAT(o.paymentTime, '%T')", ':filter'),
                )
            )
                ->setParameter('filter', '%' . $filter . '%');
        }

        if ($sortBy !== null) {
            $qb->addOrderBy('p.' . $sortBy, $sortOrder);
        }

        return $qb;
    }

    /**
     * @param string $clientId
     * @return QueryBuilder
     */
    private function buildFilterQuery(string $clientId): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');
        $qb->innerJoin('p.order', 'o')
            ->where($qb->expr()->eq('o.client', ':client'))
            ->setParameter('client', $clientId);

        return $qb;
    }
}
