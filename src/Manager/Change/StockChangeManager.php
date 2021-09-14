<?php

declare(strict_types=1);

namespace App\Manager\Change;

use App\Entity\Change\StockChange;
use App\Entity\Document\StockDocument;
use App\Exception\AppException;
use App\Manager\CharacteristicManager;
use App\Repository\Change\StockChangeRepository;
use App\Repository\Document\StockDocumentRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use Symfony\Component\HttpFoundation\Response;

class StockChangeManager
{
    use EntityManagerAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var CharacteristicManager
     */
    private CharacteristicManager $characteristicManager;

    /**
     * @var StockDocumentRepository
     */
    private StockDocumentRepository $stockDocumentRepository;

    /**
     * @var StockChangeRepository
     */
    private StockChangeRepository $stockChangeRepository;

    /**
     * @param CharacteristicManager $characteristicManager
     * @param StockDocumentRepository $stockDocumentRepository
     * @param StockChangeRepository $stockChangeRepository
     */
    public function __construct(
        CharacteristicManager $characteristicManager,
        StockDocumentRepository $stockDocumentRepository,
        StockChangeRepository $stockChangeRepository
    ) {
        $this->characteristicManager = $characteristicManager;
        $this->stockDocumentRepository = $stockDocumentRepository;
        $this->stockChangeRepository = $stockChangeRepository;
    }

    /**
     * @param string $stockDocumentId
     * @param string $characteristicId
     * @param float $value
     * @param bool $isSet
     * @return StockChange
     * @throws AppException
     */
    public function create(
        string $stockDocumentId,
        string $characteristicId,
        float $value,
        bool $isSet = false
    ): StockChange {

        $document = $this->stockDocumentRepository->find($stockDocumentId);
        $characteristic = $this->characteristicManager->get($characteristicId);

        if (!$document instanceof StockDocument) {
            throw new AppException("Stock document was not found!", Response::HTTP_NOT_FOUND);
        }

        $stockChange = new StockChange($document, $characteristic, $value, $isSet);

        $this->entityManager->persist($stockChange);

        $document->addStockChange($stockChange);

        $this->entityManager->flush();

        return $stockChange;
    }

    /**
     * @param string $id
     * @return StockChange
     * @throws AppException
     */
    public function get(string $id): StockChange
    {
        $result = $this->stockChangeRepository->find($id);

        if (!$result instanceof StockChange) {
            throw new AppException("Stock change was not found!", Response::HTTP_NOT_FOUND);
        }

        return $result;
    }
}
