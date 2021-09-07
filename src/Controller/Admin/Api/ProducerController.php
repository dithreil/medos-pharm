<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\ProducerManager;
use App\Model\Producer\CreateProducerSchema;
use App\Serializer\Normalizer\ProducerNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/producers")
 */
class ProducerController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.admin.api.producers.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление производителями"},
     *     summary="Список производителей",
     *     description="Получение списка производителей",
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
     *         description="Фильтр для названия, страны",
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
     * @param ProducerManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, ProducerManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [ProducerNormalizer::CONTEXT_TYPE_KEY => ProducerNormalizer::TYPE_IN_LIST]);
    }

    /**
     * @Route(path="", methods={"POST"}, name="app.admin.api.producers.post_create")
     * @OA\Post(
     *     tags={"Админка. Управление производителями"},
     *     summary="Создание производителя",
     *     description="Создание нового производителя",
     *     @OA\RequestBody(
     *         description="Данные производителя",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *             @OA\Property(property="shortName", description="Короткое название производителя", type="string"),
     *             @OA\Property(property="fullName", description="Полное название производителя", type="string"),
     *             @OA\Property(property="country", description="Страна производителя", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="id"),
     *             @OA\Property(property="shortName", example="Bayer", type="string"),
     *             @OA\Property(property="fullName", example="Bayer GBMH", type="string"),
     *             @OA\Property(property="country", example="Германия", type="string"),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @param ProducerManager $manager
     * @param ValidatorInterface $validator
     * @param CreateProducerSchema $producerSchema
     * @return JsonResponse
     */
    public function createAction(
        ProducerManager $manager,
        ValidatorInterface $validator,
        CreateProducerSchema $producerSchema
    ): JsonResponse {
        try {
            $errors = $validator->validate($producerSchema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $producer = $manager->create(
                $producerSchema->shortName,
                $producerSchema->fullName,
                $producerSchema->country
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($producer, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, name="app.admin.api.producers.put_edit")
     * @OA\Put(
     *     tags={"Админка. Управление производителями"},
     *     summary="Редактирование производителя",
     *     description="Редактирование производителя",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id производителя",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Данные производителя",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *             @OA\Property(property="shortName", description="Короткое название производителя", type="string"),
     *             @OA\Property(property="fullName", description="Полное название производителя", type="string"),
     *             @OA\Property(property="country", description="Страна производителя", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="id"),
     *             @OA\Property(property="shortName", example="Bayer", type="string"),
     *             @OA\Property(property="fullName", example="Bayer GBMH", type="string"),
     *             @OA\Property(property="country", example="Германия", type="string"),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @param ProducerManager $manager
     * @param ValidatorInterface $validator
     * @param CreateProducerSchema $producerSchema
     * @param string $id
     * @return JsonResponse
     */
    public function editAction(
        ProducerManager $manager,
        ValidatorInterface $validator,
        CreateProducerSchema $producerSchema,
        string $id
    ): JsonResponse {
        try {
            $errors = $validator->validate($producerSchema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $producer = $manager->edit(
                $id,
                $producerSchema->shortName,
                $producerSchema->fullName,
                $producerSchema->country
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($producer, Response::HTTP_OK);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.admin.api.producers.get_details")
     * @OA\Get(
     *     tags={"Админка. Управление производителями"},
     *     summary="Просмотр одного производителя",
     *     description="Данные одного производителя",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id производителя",
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
     *             @OA\Property(property="shortName", example="Bayer", type="string"),
     *             @OA\Property(property="fullName", example="Bayer GBMH", type="string"),
     *             @OA\Property(property="country", example="Германия", type="string"),
     *             @OA\Property(property="createTime", type="string", example="24.05.2021 17:38:35"),
     *             @OA\Property(property="updateTime", type="string", example="26.05.2021 13:20:15"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Производитель не найден"
     *     )
     * )
     * @param ProducerManager $manager
     * @param string $id
     * @return JsonResponse
     */
    public function detailsAction(string $id, ProducerManager $manager): JsonResponse
    {
        try {
            $payload = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
