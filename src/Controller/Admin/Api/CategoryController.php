<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Manager\AreaManager;
use App\Manager\CategoryManager;
use App\Serializer\Normalizer\CategoryNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/categories")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.admin.api.categories.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление категориями"},
     *     summary="Список категорий",
     *     description="Получение списка категорий",
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
     * @param CategoryManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, CategoryManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [CategoryNormalizer::CONTEXT_TYPE_KEY => CategoryNormalizer::TYPE_IN_USER]);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.admin.api.categories.get_details")
     * @OA\Get(
     *     tags={"Админка. Управление категориями"},
     *     summary="Данные категорий",
     *     description="Данные категории",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id категории",
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
     *             @OA\Property(property="code", type="string", example="V"),
     *             @OA\Property(property="name", type="string", example="name"),
     *             @OA\Property(property="createTime", type="string", example="23.03.2021"),
     *             @OA\Property(property="updateTime", type="string", example="24.03.2021"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Категория не найдена"
     *     )
     * )
     * @param CategoryManager $categoryManager
     * @param string $id
     * @return JsonResponse
     */
    public function detailsAction(CategoryManager $categoryManager, string $id): JsonResponse
    {
        try {
            $payload = $categoryManager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
