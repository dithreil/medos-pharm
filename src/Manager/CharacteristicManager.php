<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Characteristic;
use App\Exception\AppException;
use App\Repository\CharacteristicRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use App\Utils\DateTimeUtils;
use Symfony\Component\HttpFoundation\Response;

class CharacteristicManager
{
    use EntityManagerAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var NomenclatureManager
     */
    private NomenclatureManager $nomenclatureManager;

    /**
     * @var CharacteristicRepository
     */
    private CharacteristicRepository $characteristicRepository;

    /**
     * @param NomenclatureManager $nomenclatureManager
     * @param CharacteristicRepository $characteristicRepository
     */
    public function __construct(
        NomenclatureManager $nomenclatureManager,
        CharacteristicRepository $characteristicRepository
    ) {
        $this->nomenclatureManager = $nomenclatureManager;
        $this->characteristicRepository = $characteristicRepository;
    }

    /**
     * @param string $nomenclatureId
     * @param string $serial
     * @param string $expire
     * @return Characteristic
     * @throws AppException
     */
    public function create(string $nomenclatureId, string $serial, string $expire): Characteristic
    {
        $nomenclature = $this->nomenclatureManager->get($nomenclatureId);
        $expireTime = DateTimeUtils::parse($expire);

        $characteristic = new Characteristic($nomenclature, $serial, $expireTime);

        $this->entityManager->persist($characteristic);
        $this->entityManager->flush();

        return $characteristic;
    }

    /**
     * @param string $id
     * @param string $nomenclatureId
     * @param string $serial
     * @param string $expire
     * @return Characteristic
     * @throws AppException
     */
    public function edit(string $id, string $nomenclatureId, string $serial, string $expire): Characteristic
    {
        $characteristic = $this->get($id);
        $nomenclature = $this->nomenclatureManager->get($nomenclatureId);
        $expireTime = DateTimeUtils::parse($expire);

        if ($characteristic->getNomenclature() !== $nomenclature) {
            $oldNomenclature = $characteristic->getNomenclature();
            $oldNomenclature->removeCharacteristic($characteristic);

            $characteristic->setNomenclature($nomenclature);
            $nomenclature->addCharacteristic($characteristic);
        }

        $characteristic->setSerial($serial);
        $characteristic->setExpireTime($expireTime);

        $this->entityManager->flush();

        return $characteristic;
    }

    /**
     * @param string $id
     * @return Characteristic
     * @throws AppException
     */
    public function get(string $id): Characteristic
    {
        $result = $this->characteristicRepository->find($id);

        if (!$result instanceof Characteristic) {
            throw new AppException("Characteristic was not found!", Response::HTTP_NOT_FOUND);
        }

        return $result;
    }
}
