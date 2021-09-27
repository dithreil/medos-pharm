<?php

declare(strict_types=1);

namespace App\Manager\Document;

use App\DataProvider\DocumentDataProvider;
use App\Entity\Change\PriceChange;
use App\Entity\Change\StockChange;
use App\Entity\Document\Income;
use App\Entity\Price;
use App\Exception\AppException;
use App\Manager\Change\PriceChangeManager;
use App\Manager\Change\StockChangeManager;
use App\Manager\CharacteristicManager;
use App\Manager\PriceManager;
use App\Manager\StoreManager;
use App\Manager\SupplierManager;
use App\Model\PaginatedDataModel;
use App\Repository\Document\IncomeRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use App\Utils\DateTimeUtils;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\HttpFoundation\Response;

class IncomeManager
{
    use EntityManagerAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var IncomeRepository
     */
    private IncomeRepository $incomeRepository;

    /**
     * @var StockDocumentManager
     */
    private StockDocumentManager $stockDocumentManager;

    /**
     * @var StockChangeManager
     */
    private StockChangeManager $stockChangeManager;

    /**
     * @var PriceManager
     */
    private PriceManager $priceManager;

    /**
     * @var PriceChangeManager
     */
    private PriceChangeManager $priceChangeManager;

    /**
     * @var PriceDocumentManager
     */
    private PriceDocumentManager $priceDocumentManager;

    /**
     * @var CharacteristicManager
     */
    private CharacteristicManager $characteristicManager;

    /**
     * @var SupplierManager
     */
    private SupplierManager $supplierManager;

    /**
     * @var StoreManager
     */
    private StoreManager $storeManager;

    /**
     * @param IncomeRepository $incomeRepository
     * @param CharacteristicManager $characteristicManager
     * @param PriceManager $priceManager
     * @param PriceChangeManager $priceChangeManager
     * @param PriceDocumentManager $priceDocumentManager
     * @param StockChangeManager $stockChangeManager
     * @param StockDocumentManager $stockDocumentManager
     * @param StoreManager $storeManager
     * @param SupplierManager $supplierManager
     */
    public function __construct(
        IncomeRepository $incomeRepository,
        CharacteristicManager $characteristicManager,
        PriceManager $priceManager,
        PriceChangeManager $priceChangeManager,
        PriceDocumentManager $priceDocumentManager,
        StockChangeManager $stockChangeManager,
        StockDocumentManager $stockDocumentManager,
        SupplierManager $supplierManager,
        StoreManager $storeManager
    ) {
        $this->incomeRepository = $incomeRepository;
        $this->characteristicManager = $characteristicManager;
        $this->priceManager = $priceManager;
        $this->priceChangeManager = $priceChangeManager;
        $this->priceDocumentManager = $priceDocumentManager;
        $this->stockChangeManager = $stockChangeManager;
        $this->stockDocumentManager = $stockDocumentManager;
        $this->supplierManager = $supplierManager;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $date
     * @param string $supplierId
     * @param string $storeId
     * @param array $rows
     * @param string|null $comment
     * @return Income
     * @throws AppException
     */
    public function create(string $date, string $supplierId, string $storeId, array $rows, ?string $comment): Income
    {
        $supplier = $this->supplierManager->get($supplierId);
        $store = $this->storeManager->get($storeId);
        $butch = DateTimeUtils::now()->getTimestamp();
        $stockDocument = $this->stockDocumentManager->create($storeId, DocumentDataProvider::TYPE_INCOME);
        $priceDocument = $this->priceDocumentManager->create($storeId, DocumentDataProvider::TYPE_INCOME);

        foreach ($rows as $row) {
            $characteristic = $this->characteristicManager->create(
                $row['nomenclature'],
                $row['serial'],
                $butch,
                $row['expire']
            );
            $priceChange = $this->priceChangeManager->create(
                $priceDocument->getId(),
                $characteristic->getId(),
                $row['purchasePrice'],
                $row['retailPrice']
            );
            $stockChange = $this->stockChangeManager->create(
                $stockDocument->getId(),
                $characteristic->getId(),
                $row['value'],
                $priceChange
            );
            $priceChange->setStockChange($stockChange);
        }

        $income = new Income(
            $supplier,
            $store,
            $stockDocument,
            $priceDocument,
            DateTimeUtils::parse($date),
            false,
            $comment
        );

        $this->entityManager->persist($income);

        $amount = $this->getAmount($income->getId());
        $income->setAmount($amount);
        $stockDocument->setIncome($income);
        $priceDocument->setIncome($income);
        $this->entityManager->flush();

        return $income;
    }

    /**
     * @param string $id
     * @param string $date
     * @param string $supplierId
     * @param string $storeId
     * @param array $rows
     * @param string|null $comment
     * @return Income
     * @throws AppException
     */
    public function edit(
        string $id,
        string $date,
        string $supplierId,
        string $storeId,
        array $rows,
        ?string $comment
    ): Income {
        $income = $this->get($id);

        if ($income->isSet()) {
            throw new AppException("Current income already entered, action not allowed!");
        }

        $newSupplier = $this->supplierManager->get($supplierId);
        $newStore = $this->storeManager->get($storeId);
        $butch = DateTimeUtils::now()->getTimestamp();

        $stockDocument = $income->getStockDocument();
        $priceDocument = $income->getPriceDocument();

        $oldRows = $stockDocument->getStockChanges();
        $editRows = $rows;
        $sameRows = [];
        foreach ($oldRows as $oldRow) {
            $isEqualToNewRow = false;
            foreach ($editRows as $key => $row) {
                $isEqualToNewRow = $oldRow->isEqualToNewRow(
                    $row['nomenclature'],
                    $row['serial'],
                    $row['expire'],
                    $row['purchasePrice'],
                    $row['retailPrice'],
                    $row['value']
                );
                if ($isEqualToNewRow) {
                    $oldRow->getCharacteristic()->setButch($butch);
                    $sameRows[$key] = $row;
                    unset($editRows[$key]);
                    break;
                }
            }
            if (!$isEqualToNewRow) {
                $priceChange = $oldRow->getPriceChange();
                $price = $this->priceManager->findByStoreAndCharacteristic(
                    $income->getStore()->getId(),
                    $priceChange->getCharacteristic()->getId()
                );
                if ($oldRow instanceof StockChange) {
                    $this->entityManager->remove($oldRow);
                }
                if ($priceChange instanceof PriceChange) {
                    $this->entityManager->remove($priceChange);
                }
                if ($price instanceof Price) {
                    $this->entityManager->remove($price);
                }
            }
        }
        if (count($editRows) > 0) {
            foreach ($editRows as $row) {
                $characteristic = $this->characteristicManager->create(
                    $row['nomenclature'],
                    $row['serial'],
                    $butch,
                    $row['expire']
                );
                $priceChange = $this->priceChangeManager->create(
                    $priceDocument->getId(),
                    $characteristic->getId(),
                    $row['purchasePrice'],
                    $row['retailPrice']
                );
                $stockChange = $this->stockChangeManager->create(
                    $stockDocument->getId(),
                    $characteristic->getId(),
                    $row['value'],
                    $priceChange
                );
                $priceChange->setStockChange($stockChange);
            }
        }
        if ($income->getDate() !== DateTimeUtils::parse($date)) {
            $income->setDate(DateTimeUtils::parse($date));
        }
        if ($income->getStore() !== $newStore) {
            $income->setStore($newStore);
        }
        if ($income->getSupplier() !== $newSupplier) {
            $income->setSupplier($newSupplier);
        }
        $income->setComment($comment);
        $amount = $this->getAmount($income->getId());
        $income->setAmount($amount);
        $this->entityManager->flush();

        return $income;
    }

    /**
     * @param string $id
     * @return Income
     * @throws AppException
     */
    public function get(string $id): Income
    {
        $result = $this->incomeRepository->find($id);
        if (!$result instanceof Income) {
            throw new AppException("Income was not found!", Response::HTTP_NOT_FOUND);
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

            $items = $this->incomeRepository->search($filters, $page, $limit);
            $total = $this->incomeRepository->countBy($filters);

            return new PaginatedDataModel($total, $limit, $page, $items);
        } catch (UnexpectedResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    /**
     * @param string $id
     * @return float
     * @throws AppException
     */
    public function getAmount(string $id): float
    {
        $income = $this->get($id);
        return $this->stockChangeManager->getIncomeAmount($income->getStockDocument()->getId());
    }

    /**
     * @param string $id
     * @throws AppException
     */
    public function enter(string $id): void
    {
        $income = $this->get($id);

        $stockDocument = $income->getStockDocument();
        $priceDocument = $income->getPriceDocument();

        $stockDocument->setRowsIsSet(true);
        $stockDocument->setIsSet(true);
        $priceDocument->setRowsIsSet(true);
        $priceDocument->setIsSet(true);
        $income->setIsSet(true);

        $this->entityManager->flush();
    }
}
