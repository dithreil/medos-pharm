<?php

declare(strict_types=1);

namespace App\Model\Characteristic;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCharacteristicSchema extends AbstractApiSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Producer is required")
     */
    public string $nomenclature;

    /**
     * @var string
     * @Assert\NotBlank(message="Serial is required")
     * @Assert\Length(
     *     min="2",
     *     minMessage = "Serial length must be at least {{ limit }} characters long"
     * )
     */
    public string $serial;

    /**
     * @var int|null
     */
    public ?int $butch;

    /**
     * @var string
     * @Assert\NotBlank(message="Expire time is required")
     */
    public string $expire;
}
