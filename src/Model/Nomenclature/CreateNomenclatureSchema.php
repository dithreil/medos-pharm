<?php

declare(strict_types=1);

namespace App\Model\Nomenclature;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class CreateNomenclatureSchema extends AbstractApiSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Producer is required")
     */
    public string $producer;

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
     * @Assert\NotBlank(message="Medical form is required")
     */
    public string $medicalForm;

    /**
     * @Assert\NotNull(message="IsVat is required")
     * @var bool
     */
    public bool $isVat;
}
