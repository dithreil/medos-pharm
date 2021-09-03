<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Manager\OrderManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/dashboard")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route(path="/orders", methods={"GET"}, name="app.admin.api.dashboard.orders")
     * @OA\Get(
     *     tags={"Админка. Главная панель"},
     *     summary="Информация о заказах",
     *     description="Получение информации о заказах",
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="all", type="integer", example="20"),
     *             @OA\Property(property="notPaid", type="integer", example="10"),
     *             @OA\Property(property="paid", type="integer", example="10"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Неверный запрос"
     *     )
     * )
     * @param Request $request
     * @param OrderManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function ordersAction(Request $request, OrderManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->countOrders($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
