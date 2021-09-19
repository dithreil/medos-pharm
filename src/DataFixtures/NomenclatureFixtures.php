<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataProvider\NomenclatureDataProvider;
use App\Entity\Nomenclature;
use App\Entity\Producer;
use App\Exception\AppException;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NomenclatureFixtures extends Fixture implements DependentFixtureInterface
{
    public const BEPANTEN_REFERENCE = 'bepanten-nomenclature';
    public const YARINA_REFERENCE = 'yarina-nomenclature';
    public const ALMAGEL_A_REFERENCE = 'almagel-a-nomenclature';

    /**
     * @param ObjectManager $manager
     * @throws AppException
     */
    public function load(ObjectManager $manager): void
    {
        /** @var Producer $bayer */
        $bayer = $this->getReference(ProducerFixtures::BAYER_REFERENCE);
        /** @var Producer $teva */
        $teva = $this->getReference(ProducerFixtures::TEVA_REFERENCE);

        $yarina = new Nomenclature(
            $bayer,
            "Ярина таб. №21",
            NomenclatureDataProvider::getIntValueOfMedForms("Таблетки"),
            false
        );

        $manager->persist($yarina);
        $manager->flush();

        $bepanten = new Nomenclature(
            $bayer,
            "Бепантен крем 30г",
            NomenclatureDataProvider::getIntValueOfMedForms("Крем"),
            false
        );

        $manager->persist($bepanten);
        $manager->flush();

        $almagelA = new Nomenclature(
            $teva,
            "Алмагель А сусп. 170мл",
            NomenclatureDataProvider::getIntValueOfMedForms("Суспензия"),
            false
        );

        $manager->persist($almagelA);
        $manager->flush();

        $this->addReference(self::YARINA_REFERENCE, $yarina);
        $this->addReference(self::BEPANTEN_REFERENCE, $bepanten);
        $this->addReference(self::ALMAGEL_A_REFERENCE, $almagelA);
    }

    /**
     * @return array|string[]
     */
    public function getDependencies(): array
    {
        return [
            ProducerFixtures::class,
        ];
    }
}
