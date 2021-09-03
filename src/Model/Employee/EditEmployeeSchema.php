<?php

declare(strict_types=1);

namespace App\Model\Employee;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class EditEmployeeSchema extends AbstractApiSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="E-Mail is required")
     * @Assert\Email(message="E-Mail address is invalid")
     */
    public string $email;

    /**
     * @var string
     * @Assert\NotBlank(message="Phone number is required")
     * @Assert\Length(
     *     min="10",
     *     max="10",
     *     minMessage = "Phone number must be at least {{ limit }} digits long",
     *     maxMessage = "Phone number cannot be longer than {{ limit }} digits"
     * )
     */
    public string $phoneNumber;
}
