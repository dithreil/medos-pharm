<?php

declare(strict_types=1);

namespace App\Repository\Change;

use App\Entity\Change\StockChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method StockChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockChange[]    findAll()
 * @method StockChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockChangeRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockChange::class);
    }

    /**
     * @param string $stockDocumentId
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getIncomeAmountByStockDocument(string $stockDocumentId): float
    {
        $qb = $this->createQueryBuilder('s')
        ->innerJoin('s.priceChange', 'p');
        $result = $qb->where($qb->expr()->eq('s.document', ':document'))
            ->setParameter('document', $stockDocumentId)
            ->select('SUM(s.value * p.oldValue) as total')
            ->getQuery()
            ->getSingleScalarResult();

        return (float)$result;
    }
}
