<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Manager\View\WaresManager;
use App\Serializer\Normalizer\WaresNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/wares")
 */
class WaresController extends AbstractController
{
    /**
     * @Route(path="/{storeId}", methods={"GET"}, name="app.admin.api.wares.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление остатками"},
     *     summary="Список остатков",
     *     description="Получение списка остатков",
     *     @OA\Parameter(
     *         name="storeId",
     *         in="path",
     *         description="id склада",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Страница",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Количество на странице",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         description="Фильтр для названия, серии",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="total", type="integer", example="20"),
     *             @OA\Property(property="pages", type="integer", example="2"),
     *             @OA\Property(property="limit", type="integer", example="10"),
     *             @OA\Property(property="page", type="integer", example="1"),
     *             @OA\Property(property="prev", type="integer", example="1"),
     *             @OA\Property(property="next", type="integer", example="2"),
     *             @OA\Property(property="items", type="array", @OA\Items(type="object"))
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Склад не найден"
     *     )
     * )
     * @param Request $request
     * @param WaresManager $manager
     * @param string $storeId
     * @return JsonResponse
     */
    public function listAction(Request $request, WaresManager $manager, string $storeId): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($storeId, $filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [WaresNormalizer::CONTEXT_TYPE_KEY => WaresNormalizer::TYPE_IN_LIST]);
    }
}
