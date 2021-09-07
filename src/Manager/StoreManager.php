<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Store;
use App\Exception\AppException;
use App\Model\PaginatedDataModel;
use App\Repository\StoreRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\HttpFoundation\Response;

class StoreManager
{
    use EntityManagerAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var StoreRepository
     */
    private StoreRepository $storeRepository;

    /**
     * @param StoreRepository $storeRepository
     */
    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    /**
     * @param string $name
     * @param string $address
     * @param string|null $description
     * @return Store
     * @throws AppException
     */
    public function create(string $name, string $address, ?string $description): Store
    {
        $store = new Store($name, $address, $description);

        $this->entityManager->persist($store);
        $this->entityManager->flush();

        return $store;
    }

    /**
     * @param string $id
     * @param string $name
     * @param string $address
     * @param string|null $description
     * @return Store
     * @throws AppException
     */
    public function edit(string $id, string $name, string $address, ?string $description): Store
    {
        $store = $this->get($id);

        $store->setName($name);
        $store->setAddress($address);
        $store->setDescription($description);

        $this->entityManager->flush();

        return $store;
    }

    /**
     * @param string $id
     * @return Store
     * @throws AppException
     */
    public function get(string $id): Store
    {
        $result = $this->storeRepository->find($id);

        if (!$result instanceof Store) {
            throw new AppException("Store was not found!");
        }

        return $result;
    }

    /**
     * @param string $name
     * @return Store|null
     */
    public function findByName(string $name): ?Store
    {
        return $this->storeRepository->findOneBy(['name' => $name]);
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

            $items = $this->storeRepository->search($filters, $page, $limit);
            $total = $this->storeRepository->countBy($filters);

            return new PaginatedDataModel($total, $limit, $page, $items);
        } catch (UnexpectedResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }
}
