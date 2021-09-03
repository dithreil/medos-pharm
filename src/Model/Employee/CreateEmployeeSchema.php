<?php

declare(strict_types=1);

namespace App\Model\Employee;

use App\Model\User\EditUserModelSchema;
use Symfony\Component\Validator\Constraints as Assert;

class CreateEmployeeSchema extends EditUserModelSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Area is required")
     */
    public string $area;

    /**
     * @var string
     * @Assert\NotBlank(message="Speciality is required")
     */
    public string $speciality;

    /**
     * @var string
     * @Assert\NotBlank(message="Password is required")
     */
    public string $password;

    /**
     * @var int
     * @Assert\NotBlank(message="Code is required")
     * @Assert\GreaterThan(
     *     value = 0
     * )
     * @Assert\LessThan(
     *     value = 10000
     * )
     */
    public int $code;
}
