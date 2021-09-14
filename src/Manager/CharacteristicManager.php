<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Characteristic;
use App\Exception\AppException;
use App\Model\PaginatedDataModel;
use App\Repository\CharacteristicRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use App\Utils\DateTimeUtils;
use Doctrine\ORM\UnexpectedResultException;
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
     * @param int $butch
     * @param string $expire
     * @return Characteristic
     * @throws AppException
     */
    public function create(string $nomenclatureId, string $serial, int $butch, string $expire): Characteristic
    {
        $nomenclature = $this->nomenclatureManager->get($nomenclatureId);
        $expireTime = DateTimeUtils::parse($expire);

        $characteristic = new Characteristic($nomenclature, $serial, $butch, $expireTime);

        $this->entityManager->persist($characteristic);

        $nomenclature->addCharacteristic($characteristic);
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

    /**
     * @param array $filters
     * @return PaginatedDataModel
     * @throws AppException
     */
    public function search(array $filters): PaginatedDataModel
    {
        try {
            $page = intval($filters['page'] ?? 1);
            $limit = intval($filters['limit'] ?? 10);

            $items = $this->characteristicRepository->search($filters, $page, $limit);
            $total = $this->characteristicRepository->countBy($filters);

            return new PaginatedDataModel($total, $limit, $page, $items);
        } catch (UnexpectedResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }
}
