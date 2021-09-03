<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DataProvider\OrderDataProvider;
use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\OrderManager;
use App\Model\Order\CreateOrderModelSchema;
use App\Model\Order\EditOrderModelSchema;
use App\Model\Order\PatchOrderModelSchema;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/orders")
 */
class OrderController extends AbstractController
{
    /**
     * @Route(path="", methods={"POST"}, name="app.api.orders.client.post_create")
     * @OA\Post(
     *     tags={"Фронт. Управление консультациями"},
     *     summary="Создание консультации клиентом",
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь не авторизован"
     *     )
     * )
     * @param ValidatorInterface $validator
     * @param CreateOrderModelSchema $orderSchema
     * @param OrderManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function createOrder(
        ValidatorInterface $validator,
        CreateOrderModelSchema $orderSchema,
        OrderManager $manager
    ): JsonResponse {
        try {
            $errors = $validator->validate($orderSchema, null, ['client-order']);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $order = $manager->createOnlineOrder(
                $orderSchema->category,
                $orderSchema->employee,
                $orderSchema->client,
                $orderSchema->startTime,
                $orderSchema->cost,
                $orderSchema->duration,
                $orderSchema->type,
                $orderSchema->communication,
                $orderSchema->clientTarget,
                $orderSchema->clientComment
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($order, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="", methods={"GET"}, name="app.api.orders.get_list")
     * @OA\Get(
     *     tags={"Фронт. Управление консультациями"},
     *     summary="Список консультаций",
     *     description="Получение списка консультаций клиентом",
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
     *         name="notPaid",
     *         in="query",
     *         description="Не оплаченные",
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="forthcoming",
     *         in="query",
     *         description="Предстоящие",
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="past",
     *         in="query",
     *         description="Прошедшие",
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="notRated",
     *         in="query",
     *         description="Без оценки",
     *         @OA\Schema(
     *             type="boolean"
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
    public function listAction(Request $request, OrderManager $manager)
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, name="app.api.orders.client.put_edit")
     * @OA\Put(
     *     tags={"Фронт. Управление консультациями"},
     *     summary="Изменение консультаций",
     *     description="Изменение консультации клиентом",
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
     *                 @OA\Property(property="cost", description="Стоимость", type="number"),
     *                 @OA\Property(property="duration", description="Длительность", type="integer"),
     *                 @OA\Property(property="type", description="Онлайн - I/оффлайн - V", type="string"),
     *                 @OA\Property(property="communication", description="Способ коммуникации: skype/whats app", type="string", nullable=true),
     *                 @OA\Property(property="clientTarget", description="Логин клиента в мессенджере", type="string", nullable=true),
     *                 @OA\Property(property="clientComment", description="Комментарий пользователя", type="string", nullable=true),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Пользователь не авторизован"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Консультация не найдена"
     *     )
     * )
     * @param ValidatorInterface $validator
     * @param EditOrderModelSchema $orderSchema
     * @param OrderManager $manager
     * @param string $id
     * @return JsonResponse
     */
    public function editAction(
        ValidatorInterface $validator,
        EditOrderModelSchema $orderSchema,
        OrderManager $manager,
        string $id
    ): JsonResponse {
        try {
            $errors = $validator->validate($orderSchema, null, ['client-order']);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $order = $manager->editOnlineOrder(
                $id,
                $orderSchema->employee,
                $orderSchema->startTime,
                $orderSchema->cost,
                $orderSchema->duration,
                $orderSchema->type,
                $orderSchema->communication,
                $orderSchema->clientTarget,
                $orderSchema->clientComment
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($order, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{id}", methods={"PATCH"}, name="app.api.orders.patch_modify")
     * @OA\Patch(
     *     tags={"Фронт. Управление консультациями"},
     *     summary="Действия над консультациями",
     *     description="",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id консультации",
     *         required=true,
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *         description="Данные консультации",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="action", description="Действие: cancel, rate", type="string"),
     *                 @OA\Property(property="rating", description="Оценка: 1 - 5", type="integer"),
     *                 @OA\Property(property="ratingComment", description="Комментарий к оценке", type="string", nullable=true),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Операция выполнена",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Требуется комментарий",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Консультация не найдена"
     *     )
     * )
     * @param string $id
     * @param ValidatorInterface $validator
     * @param OrderManager $manager
     * @param PatchOrderModelSchema $orderSchema
     * @return JsonResponse
     */
    public function patchAction(
        string $id,
        ValidatorInterface $validator,
        OrderManager $manager,
        PatchOrderModelSchema $orderSchema
    ): JsonResponse {
        try {
            $errors = $validator->validate($orderSchema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }
            switch ($orderSchema->action) {
                case OrderDataProvider::ACTION_CANCEL:
                    $manager->cancel($id);
                    break;
                case OrderDataProvider::ACTION_RATE:
                    $manager->rate($id, $orderSchema->rating, $orderSchema->ratingComment);
                    break;
                default:
                    throw new AppException('Unknown action value', Response::HTTP_BAD_REQUEST);
            }
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.api.orders.get_details")
     * @OA\Get(
     *     tags={"Фронт. Управление консультациями"},
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
     *             @OA\Property(property="paymentTime", type="string", nullable=true, example="29.05.2021 18:25:12"),
     *             @OA\Property(property="cost", type="number", example="700"),
     *             @OA\Property(property="status", type="string", example="new"),
     *             @OA\Property(property="payments", type="array", @OA\Items(type="object")),
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
}
