<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Exception\AppException;
use App\Manager\SupplierManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SupplierFixtures extends Fixture
{
    public const MEDICODON_SUPPLIER_REFERENCE = 'medicodon-supplier';

    /**
     * @var SupplierManager
     */
    private SupplierManager $supplierManager;

    /**
     * @param SupplierManager $supplierManager
     */
    public function __construct(SupplierManager $supplierManager)
    {
        $this->supplierManager = $supplierManager;
    }

    /**
     * @param ObjectManager $manager
     * @throws AppException
     */
    public function load(ObjectManager $manager): void
    {
        $medicodon = $this->supplierManager->create(
            "Медикодон",
            "ул. Павших коммунаров 95Б",
            "dubina@medicodon.com",
            "0712812221",
            "Только предоплата, доставка на следующий день"
        );
        $manager->persist($medicodon);
        $manager->flush();

        $this->addReference(self::MEDICODON_SUPPLIER_REFERENCE, $medicodon);
    }
}
