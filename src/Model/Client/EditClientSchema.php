<?php

declare(strict_types=1);

namespace App\Model\Client;

use App\Model\User\EditUserModelSchema;
use Symfony\Component\Validator\Constraints as Assert;

class EditClientSchema extends EditUserModelSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Birthdate is required")
     */
    public string $birthDate;

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
     * EditClientSchema constructor.
     * @param string $email
     * @param string $lastName
     * @param string $firstName
     * @param string $phoneNumber
     * @param string $birthDate
     * @param string|null $patronymic
     * @param string|null $snils
     * @param string|null $skype
     * @param string|null $whatsapp
     */
    public function __construct(
        string $email,
        string $lastName,
        string $firstName,
        string $phoneNumber,
        string $birthDate,
        ?string $patronymic = null,
        ?string $snils = null,
        ?string $skype = null,
        ?string $whatsapp = null
    ) {
        parent::__construct($email, $lastName, $firstName, $phoneNumber, $patronymic);

        $this->birthDate = $birthDate;
        $this->snils = $snils;
        $this->skype = $skype;
        $this->whatsapp = $whatsapp;
    }
}
