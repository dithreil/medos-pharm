<?php

declare(strict_types=1);

namespace App\Repository\Document;

use App\Entity\Document\StockDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StockDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockDocument[]    findAll()
 * @method StockDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockDocumentRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockDocument::class);
    }
}
