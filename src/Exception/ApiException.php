<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    /**
     * @var array $errors
     */
    private array $errors;

    /**
     * ApiException constructor.
     * @param \Throwable $exception
     */
    public function __construct(\Throwable $exception)
    {
        parent::__construct(
            $exception->getCode() ?: Response::HTTP_BAD_REQUEST,
            $exception->getMessage(),
            $exception
        );

        if ($exception instanceof SeveralErrorsExceptionInterface) {
            $this->errors = $exception->getErrors();
        } else {
            $this->errors = [];
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
