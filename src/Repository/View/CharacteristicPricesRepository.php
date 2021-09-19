<?php

declare(strict_types=1);

namespace App\Repository\View;

use App\Entity\View\CharacteristicPrices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CharacteristicPrices|null find($id, $lockMode = null, $lockVersion = null)
 * @method CharacteristicPrices|null findOneBy(array $criteria, array $orderBy = null)
 * @method CharacteristicPrices[]    findAll()
 * @method CharacteristicPrices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CharacteristicPricesRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CharacteristicPrices::class);
    }
}
