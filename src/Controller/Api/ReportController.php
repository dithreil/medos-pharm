<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\ReportManager;
use App\Model\Report\CreateReportModelSchema;
use App\Model\Report\EditReportModelSchema;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/reports")
 */
class ReportController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.api.reports.get_list")
     * @OA\Get(
     *     tags={"Фронт. Управление заключениями"},
     *     summary="Список заключений для клиента/врача",
     *     description="Получение списка заключений",
     *     @OA\Parameter(
     *         name="forClient",
     *         in="query",
     *         description="Относятся к клиенту",
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="forEmployee",
     *         in="query",
     *         description="Относятся к врачу",
     *         @OA\Schema(
     *             type="boolean"
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
     * @param ReportManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, ReportManager $manager): JsonResponse
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
     * @Route(path="", methods={"POST"}, name="app.api.reports.post_create")
     * @OA\Post(
     *     tags={"Фронт. Управление заключениями"},
     *     summary="Создание заключений",
     *     description="Создание нового заключения",
     *     @OA\RequestBody(
     *         description="Данные заключения",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="orderId", description="Консультация", type="string"),
     *                 @OA\Property(property="content", description="Текст заключения", type="string"),
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
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Пользователь не авторизован"
     *     )
     * )
     * @param ValidatorInterface $validator
     * @param CreateReportModelSchema $reportSchema
     * @param ReportManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function createAction(
        ValidatorInterface $validator,
        CreateReportModelSchema $reportSchema,
        ReportManager $manager
    ): JsonResponse {
        try {
            $errors = $validator->validate($reportSchema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $result = $manager->create(
                $reportSchema->orderId,
                $reportSchema->content
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($result, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, name="app.api.reports.put_edit")
     * @OA\Put(
     *     tags={"Фронт. Управление заключениями"},
     *     summary="Изменение заключений",
     *     description="Изменение заключения",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id заключения",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Данные заключения",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="employee", description="Врач", type="string"),
     *                 @OA\Property(property="order", description="Консультация", type="string"),
     *                 @OA\Property(property="content", description="Текст заключения", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Пользователь не авторизован"
     *     )
     * )
     * @param string $id
     * @param ValidatorInterface $validator
     * @param EditReportModelSchema $reportSchema
     * @param ReportManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function editReview(
        string $id,
        ValidatorInterface $validator,
        EditReportModelSchema $reportSchema,
        ReportManager $manager
    ): JsonResponse {
        try {
            $errors = $validator->validate($reportSchema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $result = $manager->edit($id, $reportSchema->employeeId, $reportSchema->orderId, $reportSchema->content);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($result, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="app.api.reports.delete")
     * @OA\Delete(
     *     tags={"Фронт. Управление заключениями"},
     *     summary="Удаление заключений",
     *     description="Удаление заключения",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id заключения",
     *         required=true,
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Response(
     *         response=204,
     *         description="Операция выполнена",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Заключение не найдено"
     *     )
     * )
     * @param ReportManager $manager
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function deleteAction(
        ReportManager $manager,
        string $id
    ): JsonResponse {
        try {
            $manager->remove($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
