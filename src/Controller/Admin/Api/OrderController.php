<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\OrderManager;
use App\Model\Order\CreateOrderModelSchema;
use App\Model\Order\EditOrderModelSchema;
use App\Serializer\Normalizer\OrderNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/orders")
 */
class OrderController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.admin.api.orders.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление консультациями"},
     *     summary="Список консультаций",
     *     description="Получение списка консультаций",
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
     *         description="Фильтр для ФИО, Email, номера телефона врача/клиента или даты/времени консультации",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="startTime",
     *         in="query",
     *         description="Дата консультации (по умолчанию вернет консультации за сегодня)",
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
     * @param OrderManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, OrderManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $orderList = $manager->searchForAdmin($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($orderList, Response::HTTP_OK, [], [OrderNormalizer::CONTEXT_TYPE_KEY => OrderNormalizer::TYPE_LIST]);
    }

    /**
     * @Route(path="", methods={"POST"}, name="app.admin.api.orders.post_create")
     * @OA\Post(
     *     tags={"Админка. Управление консультациями"},
     *     summary="Создание консультации",
     *     description="Создание новой консультации",
     *     @OA\RequestBody(
     *         description="Данные консультации",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="client", description="Клиент", type="string"),
     *                 @OA\Property(property="employee", description="Врач", type="string"),
     *                 @OA\Property(property="category", description="Категория", type="string"),
     *                 @OA\Property(property="startTime", description="Дата консультации", type="string"),
     *                 @OA\Property(property="cost", description="Стоимость", type="number"),
     *                 @OA\Property(property="type", description="Онлайн - I/оффлайн - V", type="string"),
     *                 @OA\Property(property="duration", description="Длительность", type="integer"),
     *                 @OA\Property(property="communication", description="Способ коммуникации: skype/whats app", type="string", nullable=true),
     *                 @OA\Property(property="clientTarget", description="Логин клиента в мессенджере", type="string", nullable=true),
     *                 @OA\Property(property="clientComment", description="Комментарий пользователя", type="string", nullable=true),
     *                 @OA\Property(property="employeeComment", description="Комментарий сотрудника", type="string", nullable=true),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Операция выполнена",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @param OrderManager $orderManager
     * @param ValidatorInterface $validator
     * @param CreateOrderModelSchema $orderSchema
     * @return JsonResponse
     * @throws ApiException
     */
    public function createAction(
        OrderManager $orderManager,
        ValidatorInterface $validator,
        CreateOrderModelSchema $orderSchema
    ): JsonResponse {
        try {
            $errors = $validator->validate($orderSchema, null, ['admin-order']);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $orderManager->create(
                $orderSchema->cost,
                $orderSchema->startTime,
                $orderSchema->client,
                $orderSchema->employee,
                $orderSchema->category,
                $orderSchema->type,
                $orderSchema->duration,
                $orderSchema->communication,
                $orderSchema->clientTarget,
                $orderSchema->clientComment,
                $orderSchema->employeeComment
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, name="app.admin.api.orders.put_edit_order")
     * @OA\Put(
     *     tags={"Админка. Управление консультациями"},
     *     summary="Редактирование консультации",
     *     description="Изменение данных консультации",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id консультации",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         description="Данные консультации",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="employee", description="Врач", type="string"),
     *                 @OA\Property(property="startTime", description="Дата консультации", type="string"),
     *                 @OA\Property(property="status", description="Статус консультации", type="string", enum={"new", "done", "cancelled"}),
     *                 @OA\Property(property="communication", description="Способ коммуникации: skype/whats app", type="string", nullable=true),
     *                 @OA\Property(property="clientTarget", description="Логин клиента в мессенджере", type="string", nullable=true),
     *                 @OA\Property(property="clientComment", description="Комментарий пользователя", type="string", nullable=true),
     *                 @OA\Property(property="employeeComment", description="Комментарий сотрудника", type="string", nullable=true),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Операция выполнена",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Консультация не найдена"
     *     )
     * )
     * @param OrderManager $orderManager
     * @param ValidatorInterface $validator
     * @param EditOrderModelSchema $orderSchema
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function editAction(
        OrderManager $orderManager,
        ValidatorInterface $validator,
        EditOrderModelSchema $orderSchema,
        string $id
    ): JsonResponse {
        try {
            $errors = $validator->validate($orderSchema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $orderManager->adminEdit(
                $id,
                $orderSchema->employee,
                $orderSchema->status,
                $orderSchema->startTime,
                $orderSchema->communication,
                $orderSchema->clientTarget,
                $orderSchema->clientComment,
                $orderSchema->employeeComment
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/{id}", requirements={"id"="^(?!count$).*"}, methods={"GET"}, name="app.admin.api.orders.get_details")
     * @OA\Get(
     *     tags={"Админка. Управление консультациями"},
     *     summary="Данные консультации",
     *     description="Данные консультации",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id консультации",
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
     *             @OA\Property(property="client", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="employee", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="category", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *             @OA\Property(property="startTime", type="string", example="30.05.2021 12:30:00"),
     *             @OA\Property(property="paymentTime", type="string", example="29.05.2021 18:25:12"),
     *             @OA\Property(property="cost", type="number", example="700"),
     *             @OA\Property(property="status", type="string", example="new"),
     *             @OA\Property(property="payments", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="history", type="array", @OA\Items(type="object")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Консультация не найдена"
     *     )
     * )
     * @param string $id
     * @param OrderManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function detailsAction(string $id, OrderManager $manager): JsonResponse
    {
        try {
            $payload = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }

    /**
     * @Route(path="/count", methods={"GET"}, name="app.admin.api.orders.get_count")
     * @OA\Get(
     *     tags={"Админка. Управление консультациями"},
     *     summary="Количество консультаций",
     *     description="Получение количества консультаций",
     *     @OA\Parameter(
     *         name="startTime",
     *         in="query",
     *         description="Дата начала консультации от",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="finalTime",
     *         in="query",
     *         description="Дата начала консультации до",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="all", type="integer", example="16"),
     *             @OA\Property(property="notPaid", type="integer", example="6"),
     *             @OA\Property(property="paid", type="integer", example="10"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Консультации не найдены"
     *     )
     * )
     * @param Request $request
     * @param OrderManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function countAction(Request $request, OrderManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $count = $manager->countOrders($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }
        return $this->json($count, Response::HTTP_OK);
    }
}
