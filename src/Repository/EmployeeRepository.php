<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnexpectedResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    /**
     * @param string $email
     * @return UserInterface|null
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername(string $email)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where($qb->expr()->eq('e.email', ':email'));
        $qb->andWhere($qb->expr()->eq('e.active', true));
        $qb->setParameter('email', $email);

        return $qb->getQuery()->getOneOrNullResult();
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
        $qb->select('COUNT(e.id)');

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    /**
     * @param array $filters
     * @return QueryBuilder
     */
    private function buildFilterQuery(array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e');

        $filter = $filters['filter'] ?? null;
        $sortBy = $filters['sortBy'] ?? null;
        $sortOrder = 'ASC';
        $onlyActive = $filters['active'] ?? false;

        if (array_key_exists('descending', $filters)) {
            $sortOrder = $filters['descending'] === 'true' ? 'DESC' : 'ASC';
        }

        $qb->innerJoin('e.speciality', 's')
            ->innerJoin('e.area', 'a');

        if ($filter) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like("CONCAT(e.lastName, ' ', e.firstName, ' ', e.patronymic)", ':filterName'),
                    $qb->expr()->like('e.email', ':filter'),
                    $qb->expr()->like('e.lastName', ':filter'),
                    $qb->expr()->like('e.firstName', ':filter'),
                    $qb->expr()->like('e.patronymic', ':filter'),
                    $qb->expr()->like('e.phoneNumber', ':filter'),
                    $qb->expr()->like('s.name', ':filter')
                )
            );
            $qb->setParameter('filter', '%' . $filter . '%');
        }

        if (array_key_exists('areaCode', $filters)) {
            $areaCode = intval($filters['areaCode']);
            $qb->andWhere('a.code = :areaCode')
                ->setParameter('areaCode', $areaCode);
        }

        if (array_key_exists('specialityCode', $filters)) {
            if (intval($filters['specialityCode']) !== 0) {
                $specialityCode = intval($filters['specialityCode']);
                $qb->andWhere('s.code = :specialityCode')
                    ->setParameter('specialityCode', $specialityCode);
            }
        }

        if ($onlyActive) {
            $qb->andWhere($qb->expr()->eq('e.active', true));
        }

        if ($sortBy) {
            $qb->addOrderBy('e.' . $sortBy, $sortOrder);
        }

        return $qb;
    }
}
