<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Manager\StoreManager;
use App\Serializer\Normalizer\StoreNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StoreController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.api.stores.get_list")
     * @OA\Get(
     *     tags={"Фронт. Управление торговыми точками"},
     *     summary="Список торговых точек",
     *     description="Получение списка торговых точек",
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
     *         description="Фильтр для названия, адреса",
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
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @param Request $request
     * @param StoreManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, StoreManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [StoreNormalizer::CONTEXT_TYPE_KEY => StoreNormalizer::TYPE_IN_LIST]);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.api.stores.get_details")
     * @OA\Get(
     *     tags={"Фронт. Управление торговыми точками"},
     *     summary="Просмотр одной торговой точки",
     *     description="Данные одной торговой точки",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id торговой точки",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="id"),
     *             @OA\Property(property="name", example="Аптека Медос №53", type="string"),
     *             @OA\Property(property="address", example="г. Донецк, ул. Ватутина 153б", type="string"),
     *             @OA\Property(property="description", example="При больнице", type="string", nullable=true),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Торговая точка не найдена"
     *     )
     * )
     * @param StoreManager $manager
     * @param string $id
     * @return JsonResponse
     */
    public function detailsAction(string $id, StoreManager $manager): JsonResponse
    {
        try {
            $payload = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
