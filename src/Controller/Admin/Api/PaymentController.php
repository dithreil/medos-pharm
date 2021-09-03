<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\PaymentManager;
use App\Model\Payment\CreatePaymentModelSchema;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/payments")
 */
class PaymentController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.admin.api.payments.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление транзакциями"},
     *     summary="Список транзакций",
     *     description="Получение списка транзакций",
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
     *         name="date",
     *         in="query",
     *         description="Дата транзакции",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         description="Фильтр для ФИО клиента/врача или даты платежа",
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
     * @param PaymentManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, PaymentManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->searchForAdmin($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }

    /**
     * @Route(path="", methods={"POST"}, name="app.admin.api.payments.post_create")
     * @OA\Post(
     *     tags={"Админка. Управление транзакциями"},
     *     summary="Создание транзакций",
     *     description="Создание новой транзакции",
     *     @OA\RequestBody(
     *         description="Данные транзакции",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="amount", description="Сумма", type="number"),
     *                 @OA\Property(property="order", description="Консультация", type="string"),
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
     * @param PaymentManager $paymentManager
     * @param ValidatorInterface $validator
     * @param CreatePaymentModelSchema $paymentSchema
     * @return JsonResponse
     * @throws ApiException
     */
    public function createAction(
        PaymentManager $paymentManager,
        ValidatorInterface $validator,
        CreatePaymentModelSchema $paymentSchema
    ): JsonResponse {
        try {
            $errors = $validator->validate($paymentSchema, null, ['create']);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $paymentManager->create($paymentSchema->amount, $paymentSchema->order);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, name="app.admin.api.payments.put_edit_payment")
     * @OA\Put(
     *     tags={"Админка. Управление транзакциями"},
     *     summary="Редактирование транзакции",
     *     description="Изменение данных транзакции",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id транзакции",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         description="Данные транзакции",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="status", description="Состояние транзакции", type="string"),
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
     *         description="Транзакция не найдена"
     *     )
     * )
     * @param PaymentManager $paymentManager
     * @param ValidatorInterface $validator
     * @param CreatePaymentModelSchema $paymentSchema
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function editAction(
        PaymentManager $paymentManager,
        ValidatorInterface $validator,
        CreatePaymentModelSchema $paymentSchema,
        string $id
    ) : JsonResponse {
        try {
            $errors = $validator->validate($paymentSchema, null, 'edit');

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $paymentManager->edit($id, $paymentSchema->status);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.admin.api.payments.get_details")
     * @OA\Get(
     *     tags={"Админка. Управление транзакциями"},
     *     summary="Данные транзакции",
     *     description="Данные транзакции",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id транзакции",
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
     *             @OA\Property(property="amount", type="string", example="600"),
     *             @OA\Property(property="status", type="string", example="600"),
     *             @OA\Property(property="order", type="string", example="id"),
     *             @OA\Property(property="employee", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="client", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="createTime", type="string", example="23.03.2021"),
     *             @OA\Property(property="updateTime", type="string", example="24.03.2021"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Транзакция не найдена"
     *     )
     * )
     * @param PaymentManager $manager
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function detailsAction(PaymentManager $manager, string $id): JsonResponse
    {
        try {
            $payload = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
