<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\ClientManager;
use App\Model\Client\EditClientSchema;
use App\Model\Client\RegisterClientSchema;
use App\Model\User\RestorePasswordModelSchema;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/clients")
 */
class ClientController extends AbstractController
{
    /**
     * @Route(path="", methods={"POST"}, name="app.api.clients.post_register")
     * @OA\Post(
     *     tags={"Фронт. Управление клиентами"},
     *     summary="Регистрация",
     *     description="Регистрация клиента",
     *     @OA\RequestBody(
     *         description="Данные пользователя",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="lastName", description="Фамилия", type="string"),
     *                 @OA\Property(property="firstName", description="Имя", type="string"),
     *                 @OA\Property(property="patronymic", description="Отчество", type="string"),
     *                 @OA\Property(property="email", description="Email", type="string"),
     *                 @OA\Property(property="phoneNumber", description="Телефон", type="string"),
     *                 @OA\Property(property="birthDate", description="Дата рождения", type="string"),
     *                 @OA\Property(property="password", description="Пароль", type="string"),
     *                 @OA\Property(property="confirmPassword", description="Подтверждение пароля", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Операция выполнена"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @param ClientManager $manager
     * @param ValidatorInterface $validator
     * @param RegisterClientSchema $schema
     * @return JsonResponse
     * @throws ApiException
     */
    public function register(ClientManager $manager, ValidatorInterface $validator, RegisterClientSchema $schema): JsonResponse
    {
        try {
            $errors = $validator->validate($schema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $client = $manager->register($schema);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($client, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="", methods={"PUT"}, name="app.api.clients.put_edit_profile")
     * @OA\Put(
     *     tags={"Фронт. Управление клиентами"},
     *     summary="Изменение профиля",
     *     description="Изменение профиля клиента",
     *     @OA\RequestBody(
     *         description="Данные пользователя",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="lastName", description="Фамилия", type="string"),
     *                 @OA\Property(property="firstName", description="Имя", type="string"),
     *                 @OA\Property(property="patronymic", description="Отчество", type="string"),
     *                 @OA\Property(property="email", description="Email", type="string"),
     *                 @OA\Property(property="phoneNumber", description="Телефон", type="string"),
     *                 @OA\Property(property="birthDate", description="Дата рождения", type="string"),
     *                 @OA\Property(property="snils", description="СНИЛС", type="string"),
     *                 @OA\Property(property="skype", description="Skype", type="string"),
     *                 @OA\Property(property="whatsapp", description="Whatsapp", type="string")
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
     * @param ClientManager $manager
     * @param ValidatorInterface $validator
     * @param EditClientSchema $schema
     * @return JsonResponse
     * @throws ApiException
     */
    public function editProfile(ClientManager $manager, ValidatorInterface $validator, EditClientSchema $schema): JsonResponse
    {
        try {
            $errors = $validator->validate($schema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }

            $client = $manager->editProfile($schema);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($client, Response::HTTP_OK);
    }

    /**
     * @Route(path="/restore-password", methods={"POST"}, name="app.api.clients.post_restore_password")
     * @OA\Post(
     *     tags={"Фронт. Управление клиентами"},
     *     summary="Восстановление пароля",
     *     description="Восстановление пароля клиента",
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
     * @param ClientManager $manager
     * @param ValidatorInterface $validator
     * @param RestorePasswordModelSchema $schema
     * @return JsonResponse
     * @throws ApiException
     */
    public function restorePassword(
        ClientManager $manager,
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
