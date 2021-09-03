<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request   = $event->getRequest();

        if (in_array('application/json', $request->getAcceptableContentTypes())) {
            $response = $this->createApiResponse($exception, $request->getPathInfo());
            $event->setResponse($response);
        }
    }

    /**
     * @param \Throwable $exception
     * @param string $path
     * @return JsonResponse
     */
    private function createApiResponse(\Throwable $exception, string $path): JsonResponse
    {
        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        $data = [
            'timestamp' => time(),
            'status' => $statusCode,
            'path' => $path,
        ];

        if ($exception instanceof ApiException) {
            $errors = $exception->getErrors();
        } else {
            $errors = [];
        }

        if (empty($errors)) {
            $data['error'] = $exception->getMessage();
        } else {
            $data['errors'] = $errors;
        }

        return new JsonResponse($data, $statusCode);
    }
}
