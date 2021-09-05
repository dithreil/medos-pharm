<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Nomenclature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
}
