<?php

declare(strict_types=1);

namespace App\Model\User;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class PatchUserModelSchema extends AbstractApiSchema
{
    /**
     * @var string|null
     * @Assert\NotBlank(message="Password is required", groups={"password.change"})
     */
    public ?string $password;
}
