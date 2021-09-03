<?php

declare(strict_types=1);

namespace App\Model\Client;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterClientSchema extends AbstractApiSchema
{
    /**
     * @var string
     * @Assert\Email(message="Invalid E-Mail address")
     * @Assert\NotBlank(message="E-Mail address is required")
     */
    public string $email;

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
     * @Assert\NotBlank(message="Phone number is required")
     */
    public string $phoneNumber;

    /**
     * @var string
     * @Assert\NotBlank(message="Birthdate is required")
     */
    public string $birthDate;

    /**
     * @var string
     * @Assert\NotBlank(message="Password is required")
     */
    public string $password;

    /**
     * @var string
     * @Assert\NotBlank(message="Password confrimation is required")
     */
    public string $confirmPassword;

    /**
     * @var string|null
     */
    public ?string $snils;

    /**
     * @var string|null
     */
    public ?string $skype;

    /**
     * @var string|null
     */
    public ?string $whatsapp;

    /**
     * RegisterClientSchema constructor.
     */
    public function __construct()
    {
        $this->patronymic = null;
        $this->snils = null;
        $this->skype = null;
        $this->whatsapp = null;
    }
}
