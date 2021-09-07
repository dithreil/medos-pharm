<?php

declare(strict_types=1);

namespace App\Model\Supplier;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class CreateSupplierSchema extends AbstractApiSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Name is required")
     * @Assert\Length(
     *     min="2",
     *     minMessage = "Short name length must be at least {{ limit }} characters long"
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
     * @Assert\Email(message="E-Mail address is invalid")
     */
    public ?string $email;

    /**
     * @var string|null
     * @Assert\Length(
     *     min="10",
     *     max="10",
     *     minMessage = "Phone number must be at least {{ limit }} digits long",
     *     maxMessage = "Phone number cannot be longer than {{ limit }} digits"
     * )
     */
    public ?string $phoneNumber;

    /**
     * @var string|null
     */
    public ?string $information;
}
