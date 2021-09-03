<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnexpectedResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    /**
     * ClientRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @param string $email
     * @return UserInterface|null
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername(string $email)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->where($qb->expr()->eq('c.email', ':email'));
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
        $qb->select('COUNT(c.id)');

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    /**
     * @param array $filters
     * @return QueryBuilder
     */
    private function buildFilterQuery(array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');

        $filter = $filters['filter'] ?? null;
        $sortBy = $filters['sortBy'] ?? null;
        $sortOrder = 'ASC';
        $onlyActive = filter_var($filters['active'] ?? null, FILTER_VALIDATE_BOOLEAN);

        if (array_key_exists('descending', $filters)) {
            $sortOrder = $filters['descending'] === 'true' ? 'DESC' : 'ASC';
        }

        if ($filter !== null) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like("CONCAT(c.lastName, ' ', c.firstName, ' ', c.patronymic)", ':filter'),
                    $qb->expr()->like('c.email', ':filter'),
                    $qb->expr()->like('c.lastName', ':filter'),
                    $qb->expr()->like('c.firstName', ':filter'),
                    $qb->expr()->like('c.patronymic', ':filter'),
                    $qb->expr()->like('c.phoneNumber', ':filter'),
                    $qb->expr()->like('c.snils', ':filter'),
                    $qb->expr()->like('c.skype', ':filter'),
                    $qb->expr()->like('c.whatsapp', ':filter')
                )
            );
            $qb->setParameter('filter', '%' . $filter . '%');
        }

        if ($onlyActive) {
            $qb->andWhere($qb->expr()->eq('c.active', true));
        }

        if ($sortBy !== null) {
            $qb->addOrderBy('c.' . $sortBy, $sortOrder);
        }

        return $qb;
    }
}
