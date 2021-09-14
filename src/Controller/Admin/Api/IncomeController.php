<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\Document\IncomeManager;
use App\Model\Income\CreateIncomeSchema;
use App\Serializer\Normalizer\IncomeNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/incomes")
 */
class IncomeController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.admin.api.incomes.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление приходными накладными"},
     *     summary="Список приходных накладных",
     *     description="Получение списка приходных накладных",
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
     *         description="Фильтр для склада получения, поставщика",
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
     * @param IncomeManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, IncomeManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [IncomeNormalizer::CONTEXT_TYPE_KEY => IncomeNormalizer::TYPE_IN_LIST]);
    }

    /**
     * @Route(path="", methods={"POST"}, name="app.admin.api.incomes.post_create")
     * @OA\Post(
     *     tags={"Админка. Управление приходными накладными"},
     *     summary="Создание приходных накладных",
     *     description="Создание новой приходной накладной",
     *     @OA\RequestBody(
     *         description="Данные номенклатуры",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *             @OA\Property(property="date", description="дата накладной", type="string"),
     *             @OA\Property(property="supplierId", description="id поставщика", type="string"),
     *             @OA\Property(property="storeId", description="id склада", type="string"),
     *             @OA\Property(
     *                  property="rows",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                         property="nomenclature",
     *                         type="string",
     *                         description="id номенклатуры"
     *                      ),
     *                      @OA\Property(
     *                         property="serial",
     *                         type="string",
     *                         description="серия"
     *                      ),
     *                      @OA\Property(
     *                         property="expire",
     *                         type="string",
     *                         description="срок годности"
     *                      ),
     *                      @OA\Property(
     *                         property="value",
     *                         type="number",
     *                         description="кол-во поступления"
     *                      ),
     *                      @OA\Property(
     *                         property="purchasePrice",
     *                         type="number",
     *                         description="цена закупки"
     *                      ),
     *                      @OA\Property(
     *                         property="retailPrice",
     *                         type="number",
     *                         description="цена реализации"
     *                      ),
     *                  )
     *              )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="id"),
     *             @OA\Property(property="store", type="object"),
     *             @OA\Property(property="supplier", type="object"),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @param IncomeManager $manager
     * @param ValidatorInterface $validator
     * @param CreateIncomeSchema $incomeSchema
     * @return JsonResponse
     */
    public function createAction(
        IncomeManager $manager,
        ValidatorInterface $validator,
        CreateIncomeSchema $incomeSchema
    ): JsonResponse {
        try {
            $errors = $validator->validate($incomeSchema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $income = $manager->create(
                $incomeSchema->date,
                $incomeSchema->supplierId,
                $incomeSchema->storeId,
                $incomeSchema->rows
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($income, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.admin.api.incomes.get_details")
     * @OA\Get(
     *     tags={"Админка. Управление приходными накладными"},
     *     summary="Просмотр одной приходной накладной",
     *     description="Данные одной приходной накладной",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id приходной накладной",
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
     *             @OA\Property(property="store", type="object"),
     *             @OA\Property(property="supplier", type="object"),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Приходная накладная не найдена"
     *     )
     * )
     * @param IncomeManager $manager
     * @param string $id
     * @return JsonResponse
     */
    public function detailsAction(string $id, IncomeManager $manager): JsonResponse
    {
        try {
            $payload = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
