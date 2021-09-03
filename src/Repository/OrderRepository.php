<?php

declare(strict_types=1);

namespace App\Repository;

use App\DataProvider\OrderDataProvider;
use App\Entity\Order;
use App\Exception\AppException;
use App\Utils\DateTimeUtils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * OrderRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @param string $id
     * @param int $page
     * @param int $limit
     * @param bool $includeForthcomingOrders
     * @param bool $includeNotPaidOrders
     * @param bool $includePastOrders
     * @param bool $includeNotRatedOrders
     * @param bool $includeCancelledOrders
     * @return array
     * @throws AppException
     */
    public function search(
        string $id,
        int $page,
        int $limit,
        bool $includeForthcomingOrders,
        bool $includeNotPaidOrders,
        bool $includePastOrders,
        bool $includeNotRatedOrders,
        bool $includeCancelledOrders
    ): array {
        $qb = $this->buildFilterQuery(
            $id,
            $includeForthcomingOrders,
            $includeNotPaidOrders,
            $includePastOrders,
            $includeNotRatedOrders,
            $includeCancelledOrders
        );

        $qb->setMaxResults($limit);
        $qb->setFirstResult(($page - 1) * $limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $id
     * @param bool $includeForthcomingOrders
     * @param bool $includeNotPaidOrders
     * @param bool $includePastOrders
     * @param bool $includeNotRatedOrders
     * @param bool $includeCancelledOrders
     * @return int
     * @throws AppException|NoResultException|NonUniqueResultException
     */
    public function countBy(
        string $id,
        bool $includeForthcomingOrders,
        bool $includeNotPaidOrders,
        bool $includePastOrders,
        bool $includeNotRatedOrders,
        bool $includeCancelledOrders
    ): int {
        $qb = $this->buildFilterQuery(
            $id,
            $includeForthcomingOrders,
            $includeNotPaidOrders,
            $includePastOrders,
            $includeNotRatedOrders,
            $includeCancelledOrders
        );
        $qb->select('COUNT(o.id)');

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    /**
     * @param string $id
     * @param bool $includeForthcomingOrders
     * @param bool $includeNotPaidOrders
     * @param bool $includePastOrders
     * @param bool $includeNotRatedOrders
     * @param bool $includeCancelledOrders
     * @return QueryBuilder
     * @throws AppException
     */
    private function buildFilterQuery(
        string $id,
        bool $includeForthcomingOrders,
        bool $includeNotPaidOrders,
        bool $includePastOrders,
        bool $includeNotRatedOrders,
        bool $includeCancelledOrders
    ): QueryBuilder {
        $now = DateTimeUtils::now();
        $qb = $this->createQueryBuilder('o');

        $qb->where($qb->expr()->orX(
            $qb->expr()->eq('o.employee', ':id'),
            $qb->expr()->eq('o.client', ':id'),
        ))
            ->setParameter('id', $id);

        if ($includeForthcomingOrders) {
            $qb->andWhere($qb->expr()->gte('o.startTime', ':now'))
                ->setParameter('now', $now)
                ->andWhere('o.paymentTime is NOT NULL');
        }
        if ($includeNotPaidOrders) {
            $qb->andWhere($qb->expr()->gte('o.startTime', ':now'))
                ->setParameter('now', $now)
                ->andWhere('o.paymentTime is NULL');
        }
        if ($includePastOrders) {
            $qb->andWhere($qb->expr()->lt('o.startTime', ':now'))
                ->setParameter('now', $now)
                ->andWhere($qb->expr()->eq('o.status', ':done'))
                ->setParameter('done', OrderDataProvider::STATUS_DONE);
        }
        if ($includeNotRatedOrders) {
            $qb->andWhere($qb->expr()->eq('o.status', ':done'))
                ->setParameter('done', OrderDataProvider::STATUS_DONE)
                ->andWhere('o.rating is NULL');
        }
        if ($includeCancelledOrders) {
            $qb->andWhere($qb->expr()->eq('o.status', ':cancelled'))
                ->setParameter('cancelled', OrderDataProvider::STATUS_CANCELLED);
        }

        return $qb;
    }

    /**
     * @param \DateTimeImmutable|null $startTime
     * @param \DateTimeImmutable|null $finalTime
     * @param int $page
     * @param int $limit
     * @param array $filters
     * @return array
     */
    public function searchForAdmin(?\DateTimeImmutable $startTime, ?\DateTimeImmutable $finalTime, int $page, int $limit, array $filters): array
    {
        $qb = $this->buildFilterQueryForAdmin($startTime, $finalTime, $filters);

        $qb->setMaxResults($limit);
        $qb->setFirstResult(($page - 1) * $limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \DateTimeImmutable|null $startTime
     * @param \DateTimeImmutable|null $finalTime
     * @param array $filters
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByForAdmin(?\DateTimeImmutable $startTime, ?\DateTimeImmutable $finalTime, array $filters): int
    {
        $qb = $this->buildFilterQueryForAdmin($startTime, $finalTime, $filters);
        $qb->select('COUNT(o.id)');

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    /**
     * @param \DateTimeImmutable|null $startTime
     * @param \DateTimeImmutable|null $finalTime
     * @param  array $filters
     * @return QueryBuilder
     */
    private function buildFilterQueryForAdmin(?\DateTimeImmutable $startTime, ?\DateTimeImmutable $finalTime, array $filters)
    {
        $qb = $this->createQueryBuilder('o');
        $filterName = $filters['filter'] ?? null;
        $sortBy = $filters['sortBy'] ?? null;
        $sortOrder = 'ASC';
        if (array_key_exists('descending', $filters)) {
            $sortOrder = $filters['descending'] === 'true' ? 'DESC' : 'ASC';
        }

        if ($filterName !== null) {
            $qb->innerJoin('o.client', 'c');
            $qb->innerJoin('o.employee', 'e');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like("CONCAT(c.lastName, ' ', c.firstName, ' ', c.patronymic)", ':filterName'),
                    $qb->expr()->like('c.lastName', ':filterName'),
                    $qb->expr()->like('c.firstName', ':filterName'),
                    $qb->expr()->like('c.patronymic', ':filterName'),
                    $qb->expr()->like('c.phoneNumber', ':filterName'),
                    $qb->expr()->like('c.email', ':filterName'),
                    $qb->expr()->like("CONCAT(e.lastName, ' ', e.firstName, ' ', e.patronymic)", ':filterName'),
                    $qb->expr()->like('e.lastName', ':filterName'),
                    $qb->expr()->like('e.firstName', ':filterName'),
                    $qb->expr()->like('e.patronymic', ':filterName'),
                    $qb->expr()->like('e.phoneNumber', ':filterName'),
                    $qb->expr()->like('e.email', ':filterName'),
                    $qb->expr()->like("DATE_FORMAT(o.startTime, '%d.%m.%Y')", ':filterName'),
                    $qb->expr()->like("DATE_FORMAT(o.startTime, '%T')", ':filterName'),
                )
            )
                ->setParameter('filterName', '%' . $filterName . '%');
        }

        if ($startTime !== null) {
            $qb->andWhere($qb->expr()->between('o.startTime', ':startTime', ':finalTime'))
                ->setParameter('startTime', $startTime)
                ->setParameter('finalTime', $finalTime);
        }
        if ($sortBy !== null) {
            $qb->addOrderBy('o.' . $sortBy, $sortOrder);
        } else {
            $qb->orderBy('o.startTime', 'DESC')
                ->setMaxResults(OrderDataProvider::DEFAULT_ORDER_LIMIT);
        }

        return $qb;
    }

    /**
     * @param \DateTimeImmutable|null $startTime
     * @param \DateTimeImmutable|null $finalTime
     * @param array $filters
     * @return int
     * @throws NoResultException|NonUniqueResultException
     */
    public function countAllOrders(?\DateTimeImmutable $startTime, ?\DateTimeImmutable $finalTime, array $filters): int
    {
        $qb = $this->buildFilterQueryForAdmin($startTime, $finalTime, $filters);
        $qb->select('COUNT(o.id)');

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    /**
     * @param \DateTimeImmutable|null $startTime
     * @param \DateTimeImmutable|null $finalTime
     * @param array $filters
     * @return int
     * @throws NoResultException|NonUniqueResultException
     */
    public function countNotPaidOrders(?\DateTimeImmutable $startTime, ?\DateTimeImmutable $finalTime, array $filters): int
    {
        $qb = $this->buildFilterQueryForAdmin($startTime, $finalTime, $filters)
            ->andWhere('o.paymentTime is NULL');
        $qb->select('COUNT(o.id)');

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    /**
     * @param \DateTimeImmutable|null $startTime
     * @param \DateTimeImmutable|null $finalTime
     * @param array $filters
     * @return int
     * @throws NoResultException|NonUniqueResultException
     */
    public function countPaidOrders(?\DateTimeImmutable $startTime, ?\DateTimeImmutable $finalTime, array $filters): int
    {
        $qb = $this->buildFilterQueryForAdmin($startTime, $finalTime, $filters)
            ->andWhere('o.paymentTime is not NULL');
        $qb->select('COUNT(o.id)');

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    /**
     * @param string $employee
     * @param \DateTimeImmutable $startTime
     * @param int $duration
     * @return Order|null
     * @throws NonUniqueResultException
     */
    public function findCrossingOrder(string $employee, \DateTimeImmutable $startTime, int $duration): ?Order
    {
        $finalTime = $startTime->modify(strval(--$duration) . ' minutes');

        $qb = $this->createQueryBuilder('o');
        $qb->where($qb->expr()->between('o.startTime', ':startTime', ':finalTime'))
            ->setParameter('startTime', $startTime)
            ->setParameter('finalTime', $finalTime)
            ->andWhere($qb->expr()->eq('o.employee', ':employee'))
            ->setParameter('employee', $employee)
            ->andWhere($qb->expr()->isNotNull('o.paymentTime'));

        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
