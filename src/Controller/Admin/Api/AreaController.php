<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\AreaManager;
use App\Serializer\Normalizer\AreaNormalizer;
use OpenApi\Annotations as OA;
use App\Model\Area\CreateAreaModelSchema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/areas")
 */
class AreaController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.admin.api.areas.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление регионами"},
     *     summary="Список регионов",
     *     description="Получение списка регионов",
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
     * @param AreaManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, AreaManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [AreaNormalizer::CONTEXT_TYPE_KEY => AreaNormalizer::TYPE_IN_USER]);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.admin.api.areas.get_details")
     * @OA\Get(
     *     tags={"Админка. Управление регионами"},
     *     summary="Данные региона",
     *     description="Данные региона",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id региона",
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
     *             @OA\Property(property="name", type="string", example="name"),
     *             @OA\Property(property="createTime", type="string", example="23.03.2021"),
     *             @OA\Property(property="updateTime", type="string", example="24.03.2021"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Регион не найден"
     *     )
     * )
     * @param AreaManager $areaManager
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function detailsAction(AreaManager $areaManager, string $id): JsonResponse
    {
        try {
            $payload = $areaManager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
