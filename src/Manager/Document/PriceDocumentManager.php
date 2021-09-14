<?php

declare(strict_types=1);

namespace App\Manager\Document;

use App\DataProvider\DocumentDataProvider;
use App\Entity\Document\PriceDocument;
use App\Exception\AppException;
use App\Manager\StoreManager;
use App\Repository\Document\IncomeRepository;
use App\Repository\Document\PriceDocumentRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use Symfony\Component\HttpFoundation\Response;

class PriceDocumentManager
{
    use EntityManagerAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var PriceDocumentRepository
     */
    private PriceDocumentRepository $priceDocumentRepository;

    /**
     * @var StoreManager
     */
    private StoreManager $storeManager;

    /**
     * @var IncomeRepository
     */
    private IncomeRepository $incomeRepository;

    /**
     * @param PriceDocumentRepository $priceDocumentRepository
     * @param StoreManager $storeManager
     * @param IncomeRepository $incomeRepository
     */
    public function __construct(
        PriceDocumentRepository $priceDocumentRepository,
        StoreManager $storeManager,
        IncomeRepository $incomeRepository
    ) {
        $this->priceDocumentRepository = $priceDocumentRepository;
        $this->storeManager = $storeManager;
        $this->incomeRepository = $incomeRepository;
    }

    /**
     * @param string $storeId
     * @param string $type
     * @param bool $isSet
     * @return PriceDocument
     * @throws AppException
     */
    public function create(string $storeId, string $type, bool $isSet = false): PriceDocument
    {
        $store = $this->storeManager->get($storeId);

        if (!DocumentDataProvider::isTypeAllowed($type)) {
            throw new AppException("Document type is not allowed!", Response::HTTP_BAD_REQUEST);
        }

        $priceDocument = new PriceDocument($store, $type, $isSet);

        $this->entityManager->persist($priceDocument);
        $this->entityManager->flush();

        return $priceDocument;
    }

    /**
     * @param string $id
     * @return PriceDocument
     * @throws AppException
     */
    public function get(string $id): PriceDocument
    {
        $result = $this->priceDocumentRepository->find($id);

        if (!$result instanceof PriceDocument) {
            throw new AppException("Price document is not found!", Response::HTTP_NOT_FOUND);
        }

        return $result;
    }
}
