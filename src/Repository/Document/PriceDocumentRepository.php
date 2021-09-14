<?php

declare(strict_types=1);

namespace App\Repository\Document;

use App\Entity\Document\PriceDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PriceDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceDocument[]    findAll()
 * @method PriceDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceDocumentRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceDocument::class);
    }
}
