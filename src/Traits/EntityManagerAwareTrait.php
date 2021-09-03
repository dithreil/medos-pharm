<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerAwareTrait
{
    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    /**
     * @required
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}
