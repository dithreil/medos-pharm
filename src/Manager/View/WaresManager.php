<?php

declare(strict_types=1);

namespace App\Manager\View;

use App\Exception\AppException;
use App\Model\PaginatedDataModel;
use App\Repository\View\WaresRepository;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\HttpFoundation\Response;

class WaresManager
{
    /**
     * @var WaresRepository
     */
    private WaresRepository $waresRepository;

    /**
     * @param WaresRepository $waresRepository
     */
    public function __construct(WaresRepository $waresRepository)
    {
        $this->waresRepository = $waresRepository;
    }

    /**
     * @param string $storeId
     * @param array $filters
     * @return PaginatedDataModel
     * @throws AppException
     */
    public function search(string $storeId, array $filters): PaginatedDataModel
    {
        try {
            $page = intval($filters['page'] ?? 1);
            $limit = intval($filters['limit'] ?? 10);

            $items = $this->waresRepository->search($storeId, $filters, $page, $limit);
            $total = $this->waresRepository->countBy($storeId, $filters);

            return new PaginatedDataModel($total, $limit, $page, $items);
        } catch (UnexpectedResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }
}
