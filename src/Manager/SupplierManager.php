<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Supplier;
use App\Exception\AppException;
use App\Model\PaginatedDataModel;
use App\Repository\SupplierRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\HttpFoundation\Response;

class SupplierManager
{
    use EntityManagerAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var SupplierRepository
     */
    private SupplierRepository $supplierRepository;

    /**
     * @param SupplierRepository $supplierRepository
     */
    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    /**
     * @param string $name
     * @param string $address
     * @param string|null $email
     * @param string|null $phoneNumber
     * @param string|null $information
     * @return Supplier
     * @throws AppException
     */
    public function create(
        string $name,
        string $address,
        ?string $email = null,
        ?string $phoneNumber = null,
        ?string $information = null
    ): Supplier {
        $supplier = new Supplier($name, $address, $email, $phoneNumber, $information);

        $this->entityManager->persist($supplier);
        $this->entityManager->flush();

        return $supplier;
    }

    /**
     * @param string $id
     * @param string $name
     * @param string $address
     * @param string|null $email
     * @param string|null $phoneNumber
     * @param string|null $information
     * @return Supplier
     * @throws AppException
     */
    public function edit(
        string $id,
        string $name,
        string $address,
        ?string $email = null,
        ?string $phoneNumber = null,
        ?string $information = null
    ): Supplier {
        $supplier = $this->get($id);

        $supplier->setName($name);
        $supplier->setAddress($address);
        $supplier->setEmail($email);
        $supplier->setPhoneNumber($phoneNumber);
        $supplier->setInformation($information);
        $this->entityManager->flush();

        return $supplier;
    }

    /**
     * @param string $id
     * @return Supplier
     * @throws AppException
     */
    public function get(string $id): Supplier
    {
        $result = $this->supplierRepository->find($id);
        if (!$result instanceof Supplier) {
            throw  new AppException("Supplier was not found!");
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

            $items = $this->supplierRepository->search($filters, $page, $limit);
            $total = $this->supplierRepository->countBy($filters);

            return new PaginatedDataModel($total, $limit, $page, $items);
        } catch (UnexpectedResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }
}
