<?php

declare(strict_types=1);

namespace App\Repository\Change;

use App\Entity\Change\StockChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
}
