<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Exception\AppException;
use App\Manager\ProducerManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProducerFixtures extends Fixture
{
    public const BAYER_REFERENCE = 'bayer-producer';
    public const TEVA_REFERENCE = 'teva-producer';

    /**
     * @var ProducerManager
     */
    private ProducerManager $producerManager;

    /**
     * @param ProducerManager $producerManager
     */
    public function __construct(ProducerManager $producerManager)
    {
        $this->producerManager = $producerManager;
    }

    /**
     * @param ObjectManager $manager
     * @throws AppException
     */
    public function load(ObjectManager $manager): void
    {
        $bayer = $this->producerManager->create(
            "Байер",
            "Bayer AG",
            "Германия"
        );
        $manager->persist($bayer);
        $manager->flush();

        $teva = $this->producerManager->create(
            "Тева",
            "Teva Pharmaceutical Industries Ltd",
            "Израиль"
        );
        $manager->persist($teva);
        $manager->flush();

        $this->addReference(self::BAYER_REFERENCE, $bayer);
        $this->addReference(self::TEVA_REFERENCE, $teva);
    }
}
