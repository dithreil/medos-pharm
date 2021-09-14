<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\DataProvider\NomenclatureDataProvider;
use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\NomenclatureManager;
use App\Model\Nomenclature\CreateNomenclatureSchema;
use App\Serializer\Normalizer\NomenclatureNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/nomenclatures")
 */
class NomenclatureController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.admin.api.nomenclatures.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление номенклатурой"},
     *     summary="Список номенклатуры",
     *     description="Получение списка номенклатуры",
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
     *         description="Фильтр для названия, производителя",
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
     * @param NomenclatureManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, NomenclatureManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [NomenclatureNormalizer::CONTEXT_TYPE_KEY => NomenclatureNormalizer::TYPE_IN_LIST]);
    }

    /**
     * @Route(path="/med-forms", methods={"GET"}, name="app.admin.api.nomenclatures.med_forms_get_list")
     * @OA\Get(
     *     tags={"Админка. Управление номенклатурой"},
     *     summary="Список медицинских форм выпуска номенклатуры",
     *     description="Получение списка медицинских форм",
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="payload", type="array", @OA\Items(type="string"))
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Данные не найдены"
     *     )
     * )
     * @return JsonResponse
     * @throws ApiException
     */
    public function listMedicalFormsAction(): JsonResponse
    {
        try {
            $payload = NomenclatureDataProvider::medFormsStringOnly();
        } catch (AppException $e) {
            throw new ApiException($e);
        }
        return $this->json($payload);
    }

    /**
     * @Route(path="", methods={"POST"}, name="app.admin.api.nomenclatures.post_create")
     * @OA\Post(
     *     tags={"Админка. Управление номенклатурой"},
     *     summary="Создание номенклатуры",
     *     description="Создание новой номенклатуры",
     *     @OA\RequestBody(
     *         description="Данные номенклатуры",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *             @OA\Property(property="producer", description="id производителя", type="string"),
     *             @OA\Property(property="name", description="Короткое название производителя", type="string"),
     *             @OA\Property(property="medicalForm", description="Медицинская форма", type="string"),
     *             @OA\Property(property="isVat", description="НДС/не НДС", type="boolean"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="id"),
     *             @OA\Property(property="producer", type="object"),
     *             @OA\Property(property="name", example="Визин", type="string"),
     *             @OA\Property(property="medicalForm", example="Капли", type="string"),
     *             @OA\Property(property="isVat", example="false", type="boolean"),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *             @OA\Property(property="deleteTime", type="string", example="26.05.2021 13:20:15", nullable=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @param NomenclatureManager $manager
     * @param ValidatorInterface $validator
     * @param CreateNomenclatureSchema $nomenclatureSchema
     * @return JsonResponse
     */
    public function createAction(
        NomenclatureManager $manager,
        ValidatorInterface $validator,
        CreateNomenclatureSchema $nomenclatureSchema
    ): JsonResponse {
        try {
            $errors = $validator->validate($nomenclatureSchema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $producer = $manager->create(
                $nomenclatureSchema->producer,
                $nomenclatureSchema->name,
                $nomenclatureSchema->medicalForm,
                $nomenclatureSchema->isVat
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($producer, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, name="app.admin.api.nomenclatures.put_edit")
     * @OA\Put(
     *     tags={"Админка. Управление номенклатурой"},
     *     summary="Редактирование номенклатуры",
     *     description="Редактирование номенклатуры",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id номенклатуры",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Данные номенклатуры",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *             @OA\Property(property="producer", description="id производителя", type="string"),
     *             @OA\Property(property="name", description="Короткое название производителя", type="string"),
     *             @OA\Property(property="medicalForm", description="Медицинская форма", type="string"),
     *             @OA\Property(property="isVat", description="НДС/не НДС", type="boolean"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="id"),
     *             @OA\Property(property="producer", type="object"),
     *             @OA\Property(property="name", example="Визин", type="string"),
     *             @OA\Property(property="medicalForm", example="Капли", type="string"),
     *             @OA\Property(property="isVat", example="false", type="boolean"),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *             @OA\Property(property="deleteTime", type="string", example="26.05.2021 13:20:15", nullable=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @param NomenclatureManager $manager
     * @param ValidatorInterface $validator
     * @param CreateNomenclatureSchema $nomenclatureSchema
     * @param string $id
     * @return JsonResponse
     */
    public function editAction(
        NomenclatureManager $manager,
        ValidatorInterface $validator,
        CreateNomenclatureSchema $nomenclatureSchema,
        string $id
    ): JsonResponse {
        try {
            $errors = $validator->validate($nomenclatureSchema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $producer = $manager->edit(
                $id,
                $nomenclatureSchema->producer,
                $nomenclatureSchema->name,
                $nomenclatureSchema->medicalForm,
                $nomenclatureSchema->isVat
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($producer, Response::HTTP_OK);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.admin.api.nomenclatures.get_details")
     * @OA\Get(
     *     tags={"Админка. Управление номенклатурой"},
     *     summary="Просмотр одной номенклатуры",
     *     description="Данные одной номенклатуры",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id номенклатуры",
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
     *             @OA\Property(property="producer", type="object"),
     *             @OA\Property(property="name", example="Визин", type="string"),
     *             @OA\Property(property="medicalForm", example="Капли", type="string"),
     *             @OA\Property(property="isVat", example="false", type="boolean"),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *             @OA\Property(property="deleteTime", type="string", example="26.05.2021 13:20:15", nullable=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="номенклатура не найдена"
     *     )
     * )
     * @param NomenclatureManager $manager
     * @param string $id
     * @return JsonResponse
     */
    public function detailsAction(string $id, NomenclatureManager $manager): JsonResponse
    {
        try {
            $payload = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
