<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\SupplierManager;
use App\Model\Supplier\CreateSupplierSchema;
use App\Serializer\Normalizer\SupplierNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/suppliers")
 */
class SupplierController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.admin.api.suppliers.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление поставщиками"},
     *     summary="Список поставщиков",
     *     description="Получение списка поставщиков",
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
     *         description="Фильтр для названия, адреса, телефона",
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
     * @param SupplierManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, SupplierManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [SupplierNormalizer::CONTEXT_TYPE_KEY => SupplierNormalizer::TYPE_IN_LIST]);
    }

    /**
     * @Route(path="", methods={"POST"}, name="app.admin.api.suppliers.post_create")
     * @OA\Post(
     *     tags={"Админка. Управление поставщиками"},
     *     summary="Создание поставщиками",
     *     description="Создание нового поставщика",
     *     @OA\RequestBody(
     *         description="Данные поставщика",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *             @OA\Property(property="name", description="Название поставщика", type="string"),
     *             @OA\Property(property="address", description="Адрес поставщика", type="string"),
     *             @OA\Property(property="email", description="Электронный адрес", type="string", nullable=true),
     *             @OA\Property(property="phoneNumber", description="Номер телефона", type="string", nullable=true),
     *             @OA\Property(property="information", description="Доп. информация", type="string", nullable=true),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="id"),
     *             @OA\Property(property="name", type="string", example="Мед-стиль"),
     *             @OA\Property(property="address", type="string", example="Донецк, ул. Ватутина, 49"),
     *             @OA\Property(property="email", type="string", nullable=true, example="tokov@medstyle.ru"),
     *             @OA\Property(property="phoneNumber", type="string", nullable=true, example="9284523412"),
     *             @OA\Property(property="information", type="string", nullable=true, example="Вторник - выходной"),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @param SupplierManager $manager
     * @param ValidatorInterface $validator
     * @param CreateSupplierSchema $supplierSchema
     * @return JsonResponse
     */
    public function createAction(
        SupplierManager $manager,
        ValidatorInterface $validator,
        CreateSupplierSchema $supplierSchema
    ): JsonResponse {
        try {
            $errors = $validator->validate($supplierSchema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $producer = $manager->create(
                $supplierSchema->name,
                $supplierSchema->address,
                $supplierSchema->email,
                $supplierSchema->phoneNumber,
                $supplierSchema->information
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($producer, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, name="app.admin.api.supplier.put_edit")
     * @OA\Put(
     *     tags={"Админка. Управление поставщиками"},
     *     summary="Редактирование поставщика",
     *     description="Редактирование поставщика",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id поставщика",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Данные поставщика",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *             @OA\Property(property="name", description="Название поставщика", type="string"),
     *             @OA\Property(property="address", description="Адрес поставщика", type="string"),
     *             @OA\Property(property="email", description="Электронный адрес", type="string", nullable=true),
     *             @OA\Property(property="phoneNumber", description="Номер телефона", type="string", nullable=true),
     *             @OA\Property(property="information", description="Доп. информация", type="string", nullable=true),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="id"),
     *             @OA\Property(property="name", type="string", example="Мед-стиль"),
     *             @OA\Property(property="address", type="string", example="Донецк, ул. Ватутина, 49"),
     *             @OA\Property(property="email", type="string", nullable=true, example="tokov@medstyle.ru"),
     *             @OA\Property(property="phoneNumber", type="string", nullable=true, example="9284523412"),
     *             @OA\Property(property="information", type="string", nullable=true, example="Вторник - выходной"),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @param SupplierManager $manager
     * @param ValidatorInterface $validator
     * @param CreateSupplierSchema $supplierSchema
     * @param string $id
     * @return JsonResponse
     */
    public function editAction(
        SupplierManager $manager,
        ValidatorInterface $validator,
        CreateSupplierSchema $supplierSchema,
        string $id
    ): JsonResponse {
        try {
            $errors = $validator->validate($supplierSchema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $producer = $manager->edit(
                $id,
                $supplierSchema->name,
                $supplierSchema->address,
                $supplierSchema->email,
                $supplierSchema->phoneNumber,
                $supplierSchema->information
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($producer, Response::HTTP_OK);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.admin.api.supplier.get_details")
     * @OA\Get(
     *     tags={"Админка. Управление поставщиками"},
     *     summary="Просмотр одного поставщика",
     *     description="Данные одного поставщика",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id поставщика",
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
     *             @OA\Property(property="name", type="string", example="Мед-стиль"),
     *             @OA\Property(property="address", type="string", example="Донецк, ул. Ватутина, 49"),
     *             @OA\Property(property="email", type="string", nullable=true, example="tokov@medstyle.ru"),
     *             @OA\Property(property="phoneNumber", type="string", nullable=true, example="9284523412"),
     *             @OA\Property(property="information", type="string", nullable=true, example="Вторник - выходной"),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Поставщик не найден"
     *     )
     * )
     * @param SupplierManager $manager
     * @param string $id
     * @return JsonResponse
     */
    public function detailsAction(string $id, SupplierManager $manager): JsonResponse
    {
        try {
            $payload = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
