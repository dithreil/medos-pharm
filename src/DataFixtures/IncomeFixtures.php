<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataProvider\DocumentDataProvider;
use App\Entity\Change\PriceChange;
use App\Entity\Change\StockChange;
use App\Entity\Characteristic;
use App\Entity\Document\Income;
use App\Entity\Document\PriceDocument;
use App\Entity\Document\StockDocument;
use App\Entity\Nomenclature;
use App\Entity\Price;
use App\Entity\Store;
use App\Entity\Supplier;
use App\Exception\AppException;
use App\Utils\DateTimeUtils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class IncomeFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @throws AppException
     */
    public function load(ObjectManager $manager): void
    {
        /** @var Supplier $supplier */
        $supplier = $this->getReference(SupplierFixtures::MEDICODON_SUPPLIER_REFERENCE);
        /** @var Store $store */
        $store = $this->getReference(StoreFixtures::MAIN_OFFICE_REFERENCE);
        /** @var Nomenclature $yarina */
        $yarina = $this->getReference(NomenclatureFixtures::YARINA_REFERENCE);
        /** @var Nomenclature $bepanten */
        $bepanten = $this->getReference(NomenclatureFixtures::BEPANTEN_REFERENCE);
        /** @var Nomenclature $almagelA */
        $almagelA = $this->getReference(NomenclatureFixtures::ALMAGEL_A_REFERENCE);
        $butch = DateTimeUtils::now()->getTimestamp();

        $priceDocument = new PriceDocument($store, DocumentDataProvider::TYPE_INCOME, false);

        $manager->persist($priceDocument);
        $manager->flush();

        $stockDocument = new StockDocument($store, DocumentDataProvider::TYPE_INCOME, false);

        $manager->persist($stockDocument);
        $manager->flush();

        $characteristicY = new Characteristic($yarina, "YA29212", $butch, DateTimeUtils::now()->modify('+ 2 years'));

        $manager->persist($characteristicY);
        $yarina->addCharacteristic($characteristicY);
        $manager->flush();

        $characteristicB = new Characteristic($bepanten, "B123", $butch, DateTimeUtils::now()->modify('+ 3 years'));

        $manager->persist($characteristicB);
        $bepanten->addCharacteristic($characteristicB);
        $manager->flush();

        $characteristicA = new Characteristic($almagelA, "A333", $butch, DateTimeUtils::now()->modify('+ 4 years'));

        $manager->persist($characteristicA);
        $almagelA->addCharacteristic($characteristicA);
        $manager->flush();

        $priceChangeY = new PriceChange($priceDocument, $characteristicY, 400, 500, null, false);

        $manager->persist($priceChangeY);
        $priceDocument->addPriceChange($priceChangeY);
        $manager->flush();

        $priceY = new Price($characteristicY, $store, 500);

        $manager->persist($priceY);
        $manager->flush();

        $priceChangeB = new PriceChange($priceDocument, $characteristicB, 250, 300, null, false);

        $manager->persist($priceChangeB);
        $priceDocument->addPriceChange($priceChangeB);
        $manager->flush();

        $priceB = new Price($characteristicB, $store, 300);

        $manager->persist($priceB);
        $manager->flush();

        $priceChangeA = new PriceChange($priceDocument, $characteristicA, 500, 650, null, false);

        $manager->persist($priceChangeA);
        $priceDocument->addPriceChange($priceChangeA);
        $manager->flush();

        $priceA = new Price($characteristicA, $store, 650);

        $manager->persist($priceA);
        $manager->flush();

        $stockChangeY = new StockChange($stockDocument, $characteristicY, 400, $priceChangeY, false);

        $manager->persist($stockChangeY);
        $stockDocument->addStockChange($stockChangeY);
        $manager->flush();

        $stockChangeB = new StockChange($stockDocument, $characteristicB, 250, $priceChangeB, false);

        $manager->persist($stockChangeB);
        $stockDocument->addStockChange($stockChangeB);
        $manager->flush();

        $stockChangeA = new StockChange($stockDocument, $characteristicA, 150, $priceChangeA, false);

        $manager->persist($stockChangeA);
        $stockDocument->addStockChange($stockChangeA);
        $manager->flush();

        $priceChangeY->setStockChange($stockChangeY);
        $priceChangeB->setStockChange($stockChangeB);
        $priceChangeA->setStockChange($stockChangeA);

        $manager->flush();

        $income = new Income(
            $supplier,
            $store,
            $stockDocument,
            $priceDocument,
            DateTimeUtils::now()->modify('- 1 hour')
        );

        $manager->persist($income);

        $income->setAmount(297500);
        $stockDocument->setIncome($income);
        $priceDocument->setIncome($income);

        $manager->flush();
    }

    /**
     * @return array|string[]
     */
    public function getDependencies(): array
    {
        return [
            NomenclatureFixtures::class,
        ];
    }
}
