<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\DataProvider\PatchEntityDataProvider;
use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\EmployeeManager;
use App\Model\Employee\EditEmployeeSchema;
use App\Model\User\PatchUserModelSchema;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Serializer\Normalizer\EmployeeNormalizer;

/**
 * @Route(path="/employees")
 */
class EmployeeController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.admin.api.employees.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление сотрудниками"},
     *     summary="Список сотрудников",
     *     description="Получение списка сотрудников",
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
     *         description="Фильтр для ФИО, Email и номера телефона",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         description="Если указать active=1, то будут возвращены только активные пользователи",
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
     * @param EmployeeManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, EmployeeManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [EmployeeNormalizer::CONTEXT_TYPE_KEY => EmployeeNormalizer::TYPE_LIST]);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.admin.api.employees.get_details")
     * @OA\Get(
     *     tags={"Админка. Управление сотрудниками"},
     *     summary="Данные сотрудника",
     *     description="Данные сотрудника",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id пользователя",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="id"),
     *             @OA\Property(property="area", type="string", example="Регион"),
     *             @OA\Property(property="speciality", type="string", example="Специализация"),
     *             @OA\Property(property="email", type="string", example="test@mail.ru"),
     *             @OA\Property(property="code", type="int", example="1001"),
     *             @OA\Property(property="lastName", type="string", example="Иванов"),
     *             @OA\Property(property="firstName", type="string", example="Иван"),
     *             @OA\Property(property="patronymic", type="string", example="Иванович"),
     *             @OA\Property(property="fullName", type="string", example="ФИО"),
     *             @OA\Property(property="phoneNumber", type="string", example="79281111111"),
     *             @OA\Property(property="isActive", type="boolean", example="true"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="ROLE_USER"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь не найден"
     *     )
     * )
     * @param Request $request
     * @param EmployeeManager $manager
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function detailsAction(Request $request, EmployeeManager $manager, string $id): JsonResponse
    {
        try {
            $payload = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, name="app.admin.api.employees.put_edit")
     * @OA\Put(
     *     tags={"Админка. Управление сотрудниками"},
     *     summary="Изменение сотрудника",
     *     description="Изменение профиля сотрудника",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id пользователя",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         description="Данные пользователя",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="area", description="Регион", type="string"),
     *                 @OA\Property(property="speciality", description="Специализация", type="string"),
     *                 @OA\Property(property="code", description="Код пользователя", type="integer"),
     *                 @OA\Property(property="email", description="Email пользователя", type="string"),
     *                 @OA\Property(property="lastName", description="Фамилия", type="string"),
     *                 @OA\Property(property="firstName", description="Имя", type="string"),
     *                 @OA\Property(property="patronymic", description="Отчество", type="string"),
     *                 @OA\Property(property="phoneNumber", description="Телефон", type="string"),
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
     *         response=404,
     *         description="Пользователь не найден"
     *     )
     * )
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param EmployeeManager $employeeManager
     * @param EditEmployeeSchema $schema
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function editAction(
        Request $request,
        ValidatorInterface $validator,
        EmployeeManager $employeeManager,
        EditEmployeeSchema $schema,
        string $id
    ): JsonResponse {
        try {
            $errors = $validator->validate($schema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }
            $employee = $employeeManager->get($id);
            $employeeManager->edit(
                $id,
                $schema->email,
                $schema->phoneNumber
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }
        return $this->json($employee, Response::HTTP_OK);
    }

    /**
     * @Route(path="/{id}", methods={"PATCH"}, name="app.admin.api.employees.patch_modify")
     * @OA\Patch(
     *     tags={"Админка. Управление сотрудниками"},
     *     summary="Специальные действия над профилем сотрудника",
     *     description="Специальные действия над профилем сотрудника",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id пользователя",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="action",
     *         in="query",
     *         description="Действие над профилем",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Данные пользователя",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="password", description="Пароль", type="string")
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
     *         description="Пользователь не найден"
     *     )
     * )
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param EmployeeManager $manager
     * @param PatchUserModelSchema $schema
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function patchAction(
        Request $request,
        ValidatorInterface $validator,
        EmployeeManager $manager,
        PatchUserModelSchema $schema,
        string $id
    ): JsonResponse {
        try {
            $action = $request->query->get('action');

            if ($action === null) {
                throw new AppException('Invalid action value', Response::HTTP_BAD_REQUEST);
            }

            $errors = $validator->validate($schema, null, ['Default', $action]);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            switch ($action) {
                case PatchEntityDataProvider::ACTION_CHANGE_PASSWORD:
                    $manager->changePassword($id, $schema->password);
                    break;
                case PatchEntityDataProvider::ACTION_DISABLE:
                    $manager->disableEmployee($id);
                    break;
                case PatchEntityDataProvider::ACTION_ENABLE:
                    $manager->enableEmployee($id);
                    break;
                default:
                    throw new AppException('Unknown action value', Response::HTTP_BAD_REQUEST);
            }
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
