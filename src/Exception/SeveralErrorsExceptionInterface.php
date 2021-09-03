<?php

declare(strict_types=1);

namespace App\Exception;

interface SeveralErrorsExceptionInterface
{
    /**
     * @return array
     */
    public function getErrors(): array;
}
