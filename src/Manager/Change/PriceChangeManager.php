<?php

declare(strict_types=1);

namespace App\Manager\Change;

use App\DataProvider\DocumentDataProvider;
use App\Entity\Change\PriceChange;
use App\Entity\Document\PriceDocument;
use App\Exception\AppException;
use App\Manager\CharacteristicManager;
use App\Manager\PriceManager;
use App\Repository\Change\PriceChangeRepository;
use App\Repository\Document\PriceDocumentRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use Symfony\Component\HttpFoundation\Response;

class PriceChangeManager
{
    use EntityManagerAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var CharacteristicManager
     */
    private CharacteristicManager $characteristicManager;

    /**
     * @var PriceManager
     */
    private PriceManager $priceManager;

    /**
     * @var PriceDocumentRepository
     */
    private PriceDocumentRepository $priceDocumentRepository;

    /**
     * @var PriceChangeRepository
     */
    private PriceChangeRepository $priceChangeRepository;

    /**
     * @param CharacteristicManager $characteristicManager
     * @param PriceManager $priceManager
     * @param PriceDocumentRepository $priceDocumentRepository
     * @param PriceChangeRepository $priceChangeRepository
     */
    public function __construct(
        CharacteristicManager $characteristicManager,
        PriceManager $priceManager,
        PriceDocumentRepository $priceDocumentRepository,
        PriceChangeRepository $priceChangeRepository
    ) {
        $this->characteristicManager = $characteristicManager;
        $this->priceManager = $priceManager;
        $this->priceDocumentRepository = $priceDocumentRepository;
        $this->priceChangeRepository = $priceChangeRepository;
    }

    /**
     * @param string $priceDocumentId
     * @param string $characteristicId
     * @param float $oldValue
     * @param float $newValue
     * @param bool $isSet
     * @return PriceChange
     * @throws AppException
     */
    public function create(
        string $priceDocumentId,
        string $characteristicId,
        float $oldValue,
        float $newValue,
        bool $isSet = false
    ): PriceChange {
        $document = $this->priceDocumentRepository->find($priceDocumentId);
        $characteristic = $this->characteristicManager->get($characteristicId);

        if (!$document instanceof PriceDocument) {
            throw new AppException("Price document was not found!", Response::HTTP_NOT_FOUND);
        }

        $priceChange = new PriceChange($document, $characteristic, $oldValue, $newValue, $isSet);

        $this->entityManager->persist($priceChange);

        if ($document->getType() === DocumentDataProvider::TYPE_INCOME) {
            $this->priceManager->create($characteristic, $document->getStore(), $newValue);
        }

        $document->addPriceChange($priceChange);

        $this->entityManager->flush();

        return $priceChange;
    }

    /**
     * @param string $id
     * @return PriceChange
     * @throws AppException
     */
    public function get(string $id): PriceChange
    {
        $result = $this->priceChangeRepository->find($id);

        if (!$result instanceof PriceChange) {
            throw new AppException("Price change was not found!", Response::HTTP_NOT_FOUND);
        }

        return $result;
    }
}
