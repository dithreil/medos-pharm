<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Exception\AppException;
use App\Manager\StoreManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StoreFixtures extends Fixture
{
    public const MAIN_OFFICE_REFERENCE = 'main-office-store';

    /**
     * @var StoreManager
     */
    private StoreManager $storeManager;

    /**
     * @param StoreManager $storeManager
     */
    public function __construct(StoreManager $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @param ObjectManager $manager
     * @throws AppException
     */
    public function load(ObjectManager $manager): void
    {
        $mainOffice = $this->storeManager->create(
            "Main office",
            "Main address",
            "One to rule them all"
        );
        $manager->persist($mainOffice);
        $manager->flush();

        $this->addReference(self::MAIN_OFFICE_REFERENCE, $mainOffice);
    }
}
