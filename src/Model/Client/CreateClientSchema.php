<?php

declare(strict_types=1);

namespace App\Model\Client;

use Symfony\Component\Validator\Constraints as Assert;

class CreateClientSchema extends EditClientSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Password is required")
     */
    public string $password;
}
