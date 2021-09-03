<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiException;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/users")
 */
class UserController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app.api.users.get_profile")
     * @OA\Get(
     *     tags={"Фронт. Общие для пользователей"},
     *     summary="Получить информацию об авторизованном пользователе",
     *     description="Возвращает информацию об авторизованном пользователе или `null`, если пользователь не авторизован",
     *     @OA\Response(
     *         response=200,
     *         description="Операция выполнена"
     *     )
     * )
     * @return JsonResponse
     * @throws ApiException
     */
    public function getProfile(): JsonResponse
    {
        try {
            $payload = $this->getUser();
        } catch (\Exception $e) {
            throw new ApiException($e);
        }

        return $this->json($payload, Response::HTTP_OK);
    }
}
