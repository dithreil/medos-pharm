<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Manager\NomenclatureManager;
use App\Serializer\Normalizer\CharacteristicNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/characteristics")
 */
class CharacteristicController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.api.characteristics.get_list")
     * @OA\Get(
     *     tags={"Фронт. Управление характеристиками"},
     *     summary="Список характеристик",
     *     description="Получение списка характеристик",
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
     *         description="Фильтр для серии",
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
     * @param NomenclatureManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, NomenclatureManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [CharacteristicNormalizer::CONTEXT_TYPE_KEY => CharacteristicNormalizer::TYPE_IN_LIST]);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.api.characteristics.get_details")
     * @OA\Get(
     *     tags={"Фронт. Управление характеристиками"},
     *     summary="Просмотр одной характеристики",
     *     description="Данные одной характеристики",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id характеристики",
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
     *             @OA\Property(property="nomenclature", type="object"),
     *             @OA\Property(property="serial", example="GH8318", type="string"),
     *             @OA\Property(property="expire", example="12/2027", type="string"),
     *             @OA\Property(property="expireOriginal", example="31.12.2027 00:00:00", type="string"),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *             @OA\Property(property="deleteTime", type="string", example="26.05.2021 13:20:15", nullable=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="номенклатура не найдена"
     *     )
     * )
     * @param NomenclatureManager $manager
     * @param string $id
     * @return JsonResponse
     */
    public function detailsAction(string $id, NomenclatureManager $manager): JsonResponse
    {
        try {
            $payload = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
