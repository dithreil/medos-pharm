<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ConfirmationToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConfirmationToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfirmationToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfirmationToken[]    findAll()
 * @method ConfirmationToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfirmationTokenRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfirmationToken::class);
    }
}
