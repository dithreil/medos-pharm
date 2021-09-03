<?php

declare(strict_types=1);

namespace App\Model\User;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class EditUserModelSchema extends AbstractApiSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Last name is required")
     */
    public string $lastName;

    /**
     * @var string
     * @Assert\NotBlank(message="First name is required")
     */
    public string $firstName;

    /**
     * @var string|null
     */
    public ?string $patronymic;

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

    /**
     * EditUserModelSchema constructor.
     * @param string $email
     * @param string $lastName
     * @param string $firstName
     * @param string $phoneNumber
     * @param string|null $patronymic
     */
    public function __construct(string $email, string $lastName, string $firstName, string $phoneNumber, ?string $patronymic = null)
    {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;
        $this->patronymic = $patronymic;
    }
}
