<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\StoreManager;
use App\Model\Store\CreateStoreSchema;
use App\Serializer\Normalizer\StoreNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StoreController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.admin.api.stores.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление торговыми точками"},
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
     * @Route(path="", methods={"POST"}, name="app.admin.api.stores.post_create")
     * @OA\Post(
     *     tags={"Админка. Управление торговыми точками"},
     *     summary="Создание торговой точки",
     *     description="Создание новой торговой точки",
     *     @OA\RequestBody(
     *         description="Данные торговой точки",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *             @OA\Property(property="name", description="Название аптеки", type="string"),
     *             @OA\Property(property="address", description="Адрес аптеки", type="string"),
     *             @OA\Property(property="description", description="Описание аптеки", type="string", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="id"),
     *             @OA\Property(property="name", type="string", example="Аптека Медос №53"),
     *             @OA\Property(property="address", type="string", example="г. Донецк, ул. Ватутина 153б"),
     *             @OA\Property(property="description", nullable=true, example="При больнице", type="string"),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @param StoreManager $manager
     * @param ValidatorInterface $validator
     * @param CreateStoreSchema $storeSchema
     * @return JsonResponse
     */
    public function createAction(
        StoreManager $manager,
        ValidatorInterface $validator,
        CreateStoreSchema $storeSchema
    ): JsonResponse {
        try {
            $errors = $validator->validate($storeSchema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $producer = $manager->create(
                $storeSchema->name,
                $storeSchema->address,
                $storeSchema->description
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($producer, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, name="app.admin.api.stores.put_edit")
     * @OA\Put(
     *     tags={"Админка. Управление торговыми точками"},
     *     summary="Редактирование торговой точки",
     *     description="Редактирование торговой точки",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id торговой точки",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Данные торговой точки",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *             @OA\Property(property="name", description="Название аптеки", type="string"),
     *             @OA\Property(property="address", description="Адрес аптеки", type="string"),
     *             @OA\Property(property="description", description="Описание аптеки", type="string", nullable=true)
     *             )
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
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @param StoreManager $manager
     * @param ValidatorInterface $validator
     * @param CreateStoreSchema $storeSchema
     * @param string $id
     * @return JsonResponse
     */
    public function editAction(
        StoreManager $manager,
        ValidatorInterface $validator,
        CreateStoreSchema $storeSchema,
        string $id
    ): JsonResponse {
        try {
            $errors = $validator->validate($storeSchema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $producer = $manager->edit(
                $id,
                $storeSchema->name,
                $storeSchema->address,
                $storeSchema->description
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($producer, Response::HTTP_OK);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.admin.api.stores.get_details")
     * @OA\Get(
     *     tags={"Админка. Управление торговыми точками"},
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
