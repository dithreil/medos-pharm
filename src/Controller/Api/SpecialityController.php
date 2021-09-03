<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Manager\SpecialityManager;
use App\Serializer\Normalizer\SpecialityNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/specialities")
 */
class SpecialityController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.api.specialities.get_list")
     * @OA\Get(
     *     tags={"Фронт. Управление специальностями"},
     *     summary="Список специальностей",
     *     description="Получение списка специальностей",
     *     @OA\Parameter(
     *         name="areaId",
     *         in="query",
     *         description="id area",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Категория: 1 латинская буква - V, C, M, R, W, A",
     *         required=false,
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
     *         description="Ошибка валидации"
     *     )
     * )
     * @param Request $request
     * @param SpecialityManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, SpecialityManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->searchForClient($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
