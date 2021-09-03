<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationException;
use App\Manager\ClientManager;
use App\Manager\SecurityManager;
use App\Model\User\ChangePasswordModelSchema;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/api/security")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route(path="/login", methods={"POST"}, name="app_security_login")
     * @OA\Post(
     *     tags={"Безопасность"},
     *     summary="Авторизация",
     *     description="Авторизация пользователя в системе",
     *     @OA\RequestBody(
     *         description="Данные пользователя",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="username", description="Email пользователя", type="string"),
     *                 @OA\Property(property="password", description="Пароль пользователя", type="string"),
     *                 @OA\Property(property="user_type", description="Тип пользователя", type="string", enum={"employee", "client"})
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка авторизации"
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid credentials');
        }

        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * @Route(path="/logout", methods={"GET"}, name="app_security_logout")
     * @OA\Get(
     *     tags={"Безопасность"},
     *     summary="Выход",
     *     description="Выход пользователя",
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена"
     *     )
     * )
     * @throws \Exception
     */
    public function logout(): void
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @Route(path="/confirm-email/{token}", methods={"GET"}, name="app.security.confirm_email")
     * @OA\Get(
     *     tags={"Безопасность"},
     *     summary="Подтверждение регистрации",
     *     description="Подтверждение регистрации клиента",
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         description="Токен подтверждения регистрации",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Токен не найден"
     *     )
     * )
     * @param string $token
     * @param ClientManager $manager
     * @return Response
     */
    public function confirmEmail(string $token, ClientManager $manager): Response
    {
        $manager->confirmEmail($token);

        return $this->render('security/confirm_email.html.twig');
    }

    /**
     * @Route(path="/change-password", methods={"PUT"}, name="app.security.change-password")
     * @OA\Put(
     *     tags={"Безопасность"},
     *     summary="Замена пароля",
     *     description="Замена пароля",
     *     @OA\RequestBody(
     *         description="Cтарый и новый пароль",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="oldPassword", description="Старый пароль", type="string"),
     *                 @OA\Property(property="newPassword", description="Новый пароль", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Операция выполнена"
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
     * @param SecurityManager $manager
     * @param ValidatorInterface $validator
     * @param ChangePasswordModelSchema $schema
     * @return JsonResponse
     */
    public function changePassword(
        SecurityManager $manager,
        ValidatorInterface $validator,
        ChangePasswordModelSchema $schema
    ): JsonResponse {
        try {
            $errors = $validator->validate($schema);

            if ($errors->count() > 0) {
                throw new ConstraintsValidationException($errors, Response::HTTP_BAD_REQUEST);
            }
            $manager->changePassword($schema->oldPassword, $schema->newPassword);
        } catch (AppException $e) {
            throw new ApiException($e);
        }
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
