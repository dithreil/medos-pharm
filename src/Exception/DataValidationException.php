<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class DataValidationException extends AppException implements SeveralErrorsExceptionInterface
{
    /**
     * @var array
     */
    protected array $errors;

    /**
     * DataValidationException constructor.
     * @param array $errors
     * @param int $code
     * @param string|null $message
     */
    public function __construct(
        array $errors,
        int $code = Response::HTTP_BAD_REQUEST,
        ?string $message = null
    ) {
        parent::__construct($message ?? '', $code);

        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
