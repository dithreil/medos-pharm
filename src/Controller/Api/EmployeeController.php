<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\EmployeeManager;
use App\Model\Employee\EditEmployeeSchema;
use App\Model\User\RestorePasswordModelSchema;
use App\Serializer\Normalizer\EmployeeNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/employees")
 */
class EmployeeController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.api.employees.get_list")
     * @OA\Get(
     *     tags={"Фронт. Управление сотрудниками"},
     *     summary="Недельное расписание сотрудников",
     *     description="Получение недельного расписания сотрудников",
     *     @OA\Parameter(
     *         name="areaCode",
     *         in="query",
     *         description="Код региона: 101, 102, 103, 104, 105, 107",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="specialityCode",
     *         in="query",
     *         description="Код специальности:0 - все специальности или от 37 до 89 кроме (58,68)",
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
     *         description="Дата, вошедшая в неделю, формат: 2021-05-24",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена",
     *     ),
     *     @OA\Response(
     *         response=500,
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
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [EmployeeNormalizer::CONTEXT_TYPE_KEY => EmployeeNormalizer::TYPE_IN_WEEK_SCHEDULE]);
    }

    /**
     * @Route(path="", methods={"PUT"}, name="app.api.employees.put_edit_profile")
     * @OA\Put(
     *     tags={"Фронт. Управление сотрудниками"},
     *     summary="Изменение профиля",
     *     description="Изменение профиля сотрудника",
     *     @OA\RequestBody(
     *         description="Данные пользователя",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="area", description="Регион", type="string"),
     *                 @OA\Property(property="speciality", description="Специализация", type="string"),
     *                 @OA\Property(property="lastName", description="Фамилия", type="string"),
     *                 @OA\Property(property="firstName", description="Имя", type="string"),
     *                 @OA\Property(property="patronymic", description="Отчество", type="string"),
     *                 @OA\Property(property="email", description="Email", type="string"),
     *                 @OA\Property(property="phoneNumber", description="Телефон", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь не авторизован"
     *     )
     * )
     * @param EmployeeManager $manager
     * @param ValidatorInterface $validator
     * @param EditEmployeeSchema $schema
     * @return JsonResponse
     * @throws ApiException
     */
    public function editProfile(
        EmployeeManager $manager,
        ValidatorInterface $validator,
        EditEmployeeSchema $schema
    ): JsonResponse {
        try {
            $errors = $validator->validate($schema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $employee = $manager->editProfile($schema);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($employee, Response::HTTP_OK);
    }

    /**
     * @Route(path="/restore-password", methods={"POST"}, name="app.api.employees.post_restore_password")
     * @OA\Post(
     *     tags={"Фронт. Управление сотрудниками"},
     *     summary="Восстановление пароля",
     *     description="Восстановление пароля сотрудника",
     *     @OA\RequestBody(
     *         description="Данные пользователя",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="email", description="Email пользователя", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь не найден"
     *     )
     * )
     * @param EmployeeManager $manager
     * @param ValidatorInterface $validator
     * @param RestorePasswordModelSchema $schema
     * @return JsonResponse
     * @throws ApiException
     */
    public function restorePassword(
        EmployeeManager $manager,
        ValidatorInterface $validator,
        RestorePasswordModelSchema $schema
    ): JsonResponse {
        try {
            $errors = $validator->validate($schema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $manager->restorePassword($schema);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json(['result' => 'Если такой пользователь существует, то вам было отправлено письмо с инструкцией'], Response::HTTP_OK);
    }
}
