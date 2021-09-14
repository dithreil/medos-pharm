<?php

declare(strict_types=1);

namespace App\Manager\Document;

use App\DataProvider\DocumentDataProvider;
use App\Entity\Document\StockDocument;
use App\Exception\AppException;
use App\Manager\StoreManager;
use App\Repository\Document\IncomeRepository;
use App\Repository\Document\StockDocumentRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use Symfony\Component\HttpFoundation\Response;

class StockDocumentManager
{
    use EntityManagerAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var StockDocumentRepository
     */
    private StockDocumentRepository $stockDocumentRepository;

    /**
     * @var StoreManager
     */
    private StoreManager $storeManager;

    /**
     * @var IncomeRepository
     */
    private IncomeRepository $incomeRepository;

    /**
     * @param IncomeRepository $incomeRepository
     * @param StoreManager $storeManager
     * @param StockDocumentRepository $stockDocumentRepository
     */
    public function __construct(
        IncomeRepository $incomeRepository,
        StoreManager $storeManager,
        StockDocumentRepository $stockDocumentRepository
    ) {
        $this->incomeRepository = $incomeRepository;
        $this->storeManager = $storeManager;
        $this->stockDocumentRepository = $stockDocumentRepository;
    }

    /**
     * @param string $storeId
     * @param string $type
     * @param bool $isSet
     * @return StockDocument
     * @throws AppException
     */
    public function create(string $storeId, string $type, bool $isSet = false): StockDocument
    {
        $store = $this->storeManager->get($storeId);

        if (!DocumentDataProvider::isTypeAllowed($type)) {
            throw new AppException("Document type is not allowed!", Response::HTTP_BAD_REQUEST);
        }

        $stockDocument = new StockDocument($store, $type, $isSet);

        $this->entityManager->persist($stockDocument);
        $this->entityManager->flush();

        return $stockDocument;
    }

    /**
     * @param string $id
     * @return StockDocument
     * @throws AppException
     */
    public function get(string $id): StockDocument
    {
        $result = $this->stockDocumentRepository->find($id);

        if (!$result instanceof StockDocument) {
            throw new AppException("Stock document is not found!", Response::HTTP_NOT_FOUND);
        }

        return $result;
    }
}
