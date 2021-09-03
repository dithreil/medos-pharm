<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Manager\EmployeeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/daily-schedules")
 */
class DailyScheduleController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.admin.api.daily_schedule.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление ежедневным расписанием"},
     *     summary="Ежедневное расписание консультаций",
     *     description="Получение списка ежедневного расписания консультаций",
     *     @OA\Parameter(
     *         name="areaCode",
     *         in="query",
     *         description="Код региона: 101, 102, 103, 104, 105, 107",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *         @OA\Parameter(
     *         name="employeeCode",
     *         in="query",
     *         description="Код специалиста: целое число из запроса о недельном расписании",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="specialityCode",
     *         in="query",
     *         description="Код специальности выбранного врача",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="categoryCode",
     *         in="query",
     *         description="Код категории: 1 латинская буква - V, C, M, R, W, A",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Желаемая дата консультации",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка в параметрах запроса"
     *     )
     * )
     * @param Request $request
     * @param EmployeeManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, EmployeeManager $manager): JsonResponse
    {
        $filters = $request->query->all();

        try {
            $payload = $manager->getDailySchedule($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload);
    }
}
