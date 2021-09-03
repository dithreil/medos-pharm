<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\DataProvider\PatchEntityDataProvider;
use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\ClientManager;
use App\Model\Client\CreateClientSchema;
use App\Model\Client\EditClientSchema;
use App\Model\User\PatchUserModelSchema;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Serializer\Normalizer\ClientNormalizer;

/**
 * @Route(path="/clients")
 */
class ClientController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.admin.api.clients.get_list")
     * @OA\Get(
     *     tags={"Админка. Управление клиентами"},
     *     summary="Список клиентов",
     *     description="Получение списка клиентов",
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
     * @param ClientManager $manager
     * @return JsonResponse
     * @throws ApiException
     */
    public function listAction(Request $request, ClientManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $payload = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK, [], [ClientNormalizer::CONTEXT_TYPE_KEY => ClientNormalizer::TYPE_LIST]);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app.admin.api.clients.get_details")
     * @OA\Get(
     *     tags={"Админка. Управление клиентами"},
     *     summary="Данные клиента",
     *     description="Данные клиента",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id пользователя",
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
     *             @OA\Property(property="email", type="string", example="test@mail.ru"),
     *             @OA\Property(property="lastName", type="string", example="Иванов"),
     *             @OA\Property(property="firstName", type="string", example="Иван"),
     *             @OA\Property(property="patronymic", type="string", example="Иванович"),
     *             @OA\Property(property="fullName", type="string", example="ФИО"),
     *             @OA\Property(property="phoneNumber", type="string", example="79281111111"),
     *             @OA\Property(property="snils", type="string", example="111-111-111 22"),
     *             @OA\Property(property="skype", type="string", example="skype"),
     *             @OA\Property(property="whatsapp", type="string", example="79281111111"),
     *             @OA\Property(property="birthDate", type="string", example="12.04.1961"),
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
     * @param ClientManager $manager
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function detailsAction(Request $request, ClientManager $manager, string $id): JsonResponse
    {
        try {
            $payload = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }

    /**
     * @Route(path="", methods={"POST"}, name="app.admin.api.clients.create")
     * @OA\Post(
     *     tags={"Админка. Управление клиентами"},
     *     summary="Создание клиента",
     *     description="Создание нового клиента",
     *     @OA\RequestBody(
     *         description="Данные пользователя",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="email", description="Email пользователя", type="string"),
     *                 @OA\Property(property="lastName", description="Фамилия", type="string"),
     *                 @OA\Property(property="firstName", description="Имя", type="string"),
     *                 @OA\Property(property="patronymic", description="Отчество", type="string"),
     *                 @OA\Property(property="phoneNumber", description="Телефон", type="string"),
     *                 @OA\Property(property="birthDate", description="Дата рождения", type="string"),
     *                 @OA\Property(property="snils", description="СНИЛС", type="string"),
     *                 @OA\Property(property="skype", description="Skype", type="string"),
     *                 @OA\Property(property="whatsapp", description="Whatsapp", type="string"),
     *                 @OA\Property(property="password", description="Пароль", type="string"),
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
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param ClientManager $manager
     * @param CreateClientSchema $schema
     * @return JsonResponse
     * @throws ApiException
     */
    public function createAction(
        Request $request,
        ValidatorInterface $validator,
        ClientManager $manager,
        CreateClientSchema $schema
    ): JsonResponse {
        try {
            $errors = $validator->validate($schema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $manager->createClient($schema);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, name="app.admin.api.clients.put_edit")
     * @OA\Put(
     *     tags={"Админка. Управление клиентами"},
     *     summary="Изменение клиента",
     *     description="Изменение профиля клиента",
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
     *                 @OA\Property(property="email", description="Email пользователя", type="string"),
     *                 @OA\Property(property="lastName", description="Фамилия", type="string"),
     *                 @OA\Property(property="firstName", description="Имя", type="string"),
     *                 @OA\Property(property="patronymic", description="Отчество", type="string"),
     *                 @OA\Property(property="phoneNumber", description="Телефон", type="string"),
     *                 @OA\Property(property="birthDate", description="Дата рождения", type="string"),
     *                 @OA\Property(property="snils", description="СНИЛС", type="string"),
     *                 @OA\Property(property="skype", description="Skype", type="string"),
     *                 @OA\Property(property="whatsapp", description="Whatsapp", type="string"),
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
     * @param ClientManager $manager
     * @param EditClientSchema $schema
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function editAction(
        Request $request,
        ValidatorInterface $validator,
        ClientManager $manager,
        EditClientSchema $schema,
        string $id
    ): JsonResponse {
        try {
            $errors = $validator->validate($schema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $client = $manager->edit($id, $schema);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($client, Response::HTTP_OK);
    }

    /**
     * @Route(path="/{id}", methods={"PATCH"}, name="app.admin.api.clients.patch_modify")
     * @OA\Patch(
     *     tags={"Админка. Управление клиентами"},
     *     summary="Специальные действия над профилем клиента",
     *     description="Специальные действия над профилем клиента",
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
     * @param ClientManager $manager
     * @param PatchUserModelSchema $schema
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function patchAction(
        Request $request,
        ValidatorInterface $validator,
        ClientManager $manager,
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
                    $manager->disableClient($id);
                    break;
                case PatchEntityDataProvider::ACTION_ENABLE:
                    $manager->enableClient($id);
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
