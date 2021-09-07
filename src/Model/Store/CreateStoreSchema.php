<?php

declare(strict_types=1);

namespace App\Model\Store;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class CreateStoreSchema extends AbstractApiSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Name is required")
     * @Assert\Length(
     *     min="2",
     *     minMessage = "Name length must be at least {{ limit }} characters long"
     * )
     */
    public string $name;

    /**
     * @var string
     * @Assert\NotBlank(message="Address is required")
     * @Assert\Length(
     *     min="10",
     *     minMessage = "Address length must be at least {{ limit }} characters long"
     * )
     */
    public string $address;

    /**
     * @var string|null
     */
    public ?string $description;
}
