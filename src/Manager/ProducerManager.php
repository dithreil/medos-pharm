<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Producer;
use App\Exception\AppException;
use App\Model\PaginatedDataModel;
use App\Repository\ProducerRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\HttpFoundation\Response;

class ProducerManager
{
    use EntityManagerAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var ProducerRepository
     */
    private ProducerRepository $producerRepository;

    /**
     * @param ProducerRepository $producerRepository
     */
    public function __construct(ProducerRepository $producerRepository)
    {
        $this->producerRepository = $producerRepository;
    }

    /**
     * @param string $shortName
     * @param string $fullName
     * @param string $country
     * @return Producer
     * @throws AppException
     */
    public function create(string $shortName, string $fullName, string $country): Producer
    {
        $producer = new Producer($shortName, $fullName, $country);

        $this->entityManager->persist($producer);
        $this->entityManager->flush();

        return $producer;
    }

    /**
     * @param string $id
     * @param string $shortName
     * @param string $fullName
     * @param string $country
     * @return Producer
     * @throws AppException
     */
    public function edit(string $id, string $shortName, string $fullName, string $country): Producer
    {
        $producer = $this->get($id);

        $producer->setShortName($shortName);
        $producer->setFullName($fullName);
        $producer->setCountry($country);

        $this->entityManager->flush();

        return $producer;
    }

    /**
     * @param string $id
     * @return Producer
     * @throws AppException
     */
    public function get(string $id): Producer
    {
        $result = $this->producerRepository->find($id);

        if (!$result instanceof Producer) {
            throw new AppException("Producer was not found!", Response::HTTP_NOT_FOUND);
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

            $items = $this->producerRepository->search($filters, $page, $limit);
            $total = $this->producerRepository->countBy($filters);

            return new PaginatedDataModel($total, $limit, $page, $items);
        } catch (UnexpectedResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }
}
