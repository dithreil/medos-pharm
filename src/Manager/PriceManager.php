<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Characteristic;
use App\Entity\Price;
use App\Entity\Store;
use App\Exception\AppException;
use App\Repository\PriceRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use Symfony\Component\HttpFoundation\Response;

class PriceManager
{
    use EntityManagerAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var PriceRepository
     */
    private PriceRepository $priceRepository;

    /**
     * @var StoreManager
     */
    private StoreManager $storeManager;

    /**
     * @param PriceRepository $priceRepository
     * @param StoreManager $storeManager
     */
    public function __construct(
        PriceRepository $priceRepository,
        StoreManager $storeManager
    ) {
        $this->priceRepository = $priceRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Characteristic $characteristic
     * @param Store $store
     * @param float $value
     * @return Price
     * @throws AppException
     */
    public function create(Characteristic $characteristic, Store $store, float $value): Price
    {
        $price = new Price($characteristic, $store, $value);

        $this->entityManager->persist($price);
        $this->entityManager->flush();

        return $price;
    }

    /**
     * @param string $id
     * @param float $value
     * @return Price
     * @throws AppException
     */
    public function edit(string $id, float $value): Price
    {
        $price = $this->get($id);
        $price->setValue($value);

        $this->entityManager->flush();

        return $price;
    }

    /**
     * @param string $id
     * @return Price
     * @throws AppException
     */
    public function get(string $id): Price
    {
        $result = $this->priceRepository->find($id);

        if (!$result instanceof Price) {
            throw new AppException("Price was not found!", Response::HTTP_NOT_FOUND);
        }

        return $result;
    }

    /**
     * @param string $store
     * @param string $characteristic
     * @return Price|null
     */
    public function findByStoreAndCharacteristic(string $store, string $characteristic): ?Price
    {
        $result = $this->priceRepository->findOneBy(['store' => $store, 'characteristic' => $characteristic]);

        return $result;
    }
}
