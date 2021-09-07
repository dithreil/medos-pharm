<?php

declare(strict_types=1);

namespace App\Model\Employee;

use App\Model\User\EditUserModelSchema;
use Symfony\Component\Validator\Constraints as Assert;

class CreateEmployeeSchema extends EditUserModelSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Password is required")
     */
    public string $password;

    /**
     * @var string
     * @Assert\NotBlank(message="Store is required")
     */
    public string $storeId;
}
