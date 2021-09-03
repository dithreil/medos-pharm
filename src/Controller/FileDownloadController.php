<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Manager\OrderDocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/download")
 */
class FileDownloadController extends AbstractController
{
    /**
     * @Route("/order-documents/{id}", methods={"GET"}, name="app.download.order_documents")
     * @param OrderDocumentManager $manager
     * @param string $id
     * @return BinaryFileResponse
     */
    public function downloadAction(OrderDocumentManager $manager, string $id) : BinaryFileResponse
    {
        try {
            $filePath = $manager->getDownloadPath($id);

            return new BinaryFileResponse($filePath);
        } catch (AppException $e) {
            throw new ApiException($e);
        }
    }
}
