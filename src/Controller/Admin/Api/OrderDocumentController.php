<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Manager\OrderDocumentManager;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/orders")
 */
class OrderDocumentController extends AbstractController
{
    /**
     * @Route("/{id}/documents", methods={"POST"}, name="app.admin.api.orders.post_upload_files")
     * @OA\Post(
     *     tags={"Админка. Управление консультациями"},
     *     summary="Загрузка документов консультации",
     *     description="Загрузка новых документов консультации",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="documentType",
     *                      description="documentType",
     *                      type="array",
     *                      @OA\Items(type="string", format="binary")
     *                   ),
     *               ),
     *           ),
     *       ),
     *     @OA\Response(
     *         response=204,
     *         description="Операция выполнена",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Несовпадение Csrf токена"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Отсутствуют файлы для обработки"
     *     )
     * )
     * @param OrderDocumentManager $orderDocumentManager
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function createAction(
        OrderDocumentManager $orderDocumentManager,
        Request $request,
        string $id
    ): JsonResponse {
        $files = $request->files->all('documentType');

        try {
            if (empty($files)) {
                throw new AppException('No files specified', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            foreach ($files as $file) {
                $orderDocumentManager->create($id, $file);
            }
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/order-documents/{id}", methods={"DELETE"}, name="app.admin.api.order_documents.delete")
     * @OA\Delete(
     *     tags={"Админка. Управление консультациями"},
     *     summary="Удаление документов консультации",
     *     description="Удаление имеющихся документов консультации",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id документа консультации",
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
     *         description="Консультация/документ не найдены"
     *     )
     * )
     * @param OrderDocumentManager $manager
     * @param string $id
     * @return JsonResponse
     * @throws ApiException
     */
    public function deleteAction(
        OrderDocumentManager $manager,
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
