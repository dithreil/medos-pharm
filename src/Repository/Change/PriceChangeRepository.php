<?php

declare(strict_types=1);

namespace App\Repository\Change;

use App\Entity\Change\PriceChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PriceChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceChange[]    findAll()
 * @method PriceChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceChangeRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceChange::class);
    }
}
