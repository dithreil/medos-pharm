<?php

declare(strict_types=1);

namespace App\Model\User;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class RestorePasswordModelSchema extends AbstractApiSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="email is required")
     */
    public string $email;
}
