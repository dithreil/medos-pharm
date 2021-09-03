<?php

declare(strict_types=1);

namespace App\Exception;

use App\Utils\ArrayUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintsValidationException extends DataValidationException
{
    /**
     * ConstraintsValidationException constructor.
     * @param ConstraintViolationListInterface $errors
     * @param int $code
     * @param string|null $message
     */
    public function __construct(
        ConstraintViolationListInterface $errors,
        int $code = Response::HTTP_BAD_REQUEST,
        ?string $message = null
    ) {
        parent::__construct(ArrayUtils::constraintsToArray($errors), $code, $message);
    }
}
