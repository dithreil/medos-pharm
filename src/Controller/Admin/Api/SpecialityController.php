<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\SpecialityManager;
use App\Model\Speciality\CreateSpecialityModelSchema;
use App\Serializer\Normalizer\SpecialityNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/specialities")
 */
class SpecialityController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.admin.api.specialities.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление специальностями"},
     *     summary="Список специальностей",
     *     description="Получение списка специальностей",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Фильтр для названия специальности",
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
     * @param SpecialityManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, SpecialityManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [SpecialityNormalizer::CONTEXT_TYPE_KEY => SpecialityNormalizer::TYPE_IN_USER]);
    }

    /**
     * @Route(path="", methods={"POST"}, name="app.admin.api.specialities.post_create")
     * @OA\Post(
     *     tags={"Админка. Управление специальностями"},
     *     summary="Создание специальности",
     *     description="Создание новой специальности",
     *     @OA\RequestBody(
     *         description="Данные специальности",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="code", description="Код специальности от 10 до 100", type="integer"),
     *                 @OA\Property(property="name", description="Название специальности", type="string"),
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
     * @param SpecialityManager $specialityManager
     * @param ValidatorInterface $validator
     * @param CreateSpecialityModelSchema $specialitySchema
     * @return JsonResponse
     * @throws ApiException
     */
    public function createAction(
        SpecialityManager $specialityManager,
        ValidatorInterface $validator,
        CreateSpecialityModelSchema $specialitySchema
    ): JsonResponse {
        try {
            $errors = $validator->validate($specialitySchema, null, ['create']);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $specialityManager->create($specialitySchema->code, $specialitySchema->name);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, name="app.admin.api.specialities.put_edit_speciality")
     * @OA\Put(
     *     tags={"Админка. Управление специальностями"},
     *     summary="Редактирование специальности",
     *     description="Изменение данных специальности",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id специальности",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         description="Данные специальности",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="code", description="Код специальности от 10 до 100", type="integer"),
     *                 @OA\Property(property="name", description="Название специальности", type="string"),
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
     *         description="Специальность не найдена"
     *     )
     * )
     * @param SpecialityManager $specialityManager
     * @param ValidatorInterface $validator
     * @param CreateSpecialityModelSchema $specialitySchema
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function editAction(
        SpecialityManager $specialityManager,
        ValidatorInterface $validator,
        CreateSpecialityModelSchema $specialitySchema,
        string $id
    ) : JsonResponse {
        try {
            $errors = $validator->validate($specialityManager, null, 'edit');

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $specialityManager->edit($id, $specialitySchema->code, $specialitySchema->name);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.admin.api.specialities.get_details")
     * @OA\Get(
     *     tags={"Админка. Управление специальностями"},
     *     summary="Данные специальностей",
     *     description="Данные специальности",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id специальности",
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
     *             @OA\Property(property="code", type="integer", example="22"),
     *             @OA\Property(property="name", type="string", example="name"),
     *             @OA\Property(property="createTime", type="string", example="23.03.2021"),
     *             @OA\Property(property="updateTime", type="string", example="24.03.2021"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Специальность не найдена"
     *     )
     * )
     * @param SpecialityManager $specialityManager
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function detailsAction(SpecialityManager $specialityManager, string $id): JsonResponse
    {
        try {
            $payload = $specialityManager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
