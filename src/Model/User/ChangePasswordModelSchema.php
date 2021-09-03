<?php

declare(strict_types=1);

namespace App\Model\User;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordModelSchema extends AbstractApiSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Old password is required")
     */
    public string $oldPassword;

    /**
     * @var string
     * @Assert\NotBlank(message="New password is required")
     */
    public string $newPassword;
}
