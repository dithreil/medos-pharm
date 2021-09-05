<?php

declare(strict_types=1);


namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Manager\ProducerManager;
use App\Serializer\Normalizer\ProducerNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProducerController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.api.producers.get_list")
     * @OA\Get(
     *     tags={"Фронт. Управление производителями"},
     *     summary="Список производителей",
     *     description="Получение списка производителей",
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
     *         description="Фильтр для названия, страны",
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
     * @param ProducerManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, ProducerManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [ProducerNormalizer::CONTEXT_TYPE_KEY => ProducerNormalizer::TYPE_IN_LIST]);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.api.producers.get_details")
     * @OA\Get(
     *     tags={"Фронт. Управление производителями"},
     *     summary="Просмотр одного производителя",
     *     description="Данные одного производителя",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id производителя",
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
     *             @OA\Property(property="shortName", description="Bayer", type="string"),
     *             @OA\Property(property="fullName", description="Bayer GBMH", type="string"),
     *             @OA\Property(property="country", description="Германия", type="string")
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Производитель не найден"
     *     )
     * )
     * @param ProducerManager $manager
     * @param string $id
     * @return JsonResponse
     */
    public function detailsAction(string $id, ProducerManager $manager): JsonResponse
    {
        try {
            $payload = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
