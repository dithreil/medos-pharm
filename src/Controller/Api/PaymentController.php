<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\PaymentManager;
use App\Model\Payment\CustomPaymentModelSchema;
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
     * @Route(path="", methods={"GET"}, name="app.api.payments.get_list")
     * @OA\Get(
     *     tags={"Фронт. Управление транзакциями"},
     *     summary="Список транзакций клиента",
     *     description="Получение списка транзакций клиента",
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         description="id клиента",
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
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }

    /**
     * @Route(path="", methods={"PUT"}, name="app.api.payments.put_payment")
     * @OA\Put(
     *     tags={"Фронт. Управление транзакциями"},
     *     summary="Оплата консультаций",
     *     description="Оплата консультации",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="order", type="string", example="asfa-jjgt-kath-adhywbga")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Операция выполнена",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации/Время уже не доступно"
     *     )
     * )
     * @param ValidatorInterface $validator
     * @param CustomPaymentModelSchema $schema
     * @param PaymentManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function paymentAction(
        ValidatorInterface $validator,
        CustomPaymentModelSchema $schema,
        PaymentManager $manager
    ): JsonResponse {
        try {
            $errors = $validator->validate($schema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $confirmationUrl = $manager->payForOrder($schema->order);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json(['confirmationUrl' => $confirmationUrl], Response::HTTP_OK);
    }
}
