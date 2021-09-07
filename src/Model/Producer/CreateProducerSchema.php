<?php

declare(strict_types=1);

namespace App\Model\Producer;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class CreateProducerSchema extends AbstractApiSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Short name is required")
     * @Assert\Length(
     *     min="2",
     *     max="20",
     *     minMessage = "Short name length must be at least {{ limit }} characters long",
     *     maxMessage = "Short name length is limited to {{ limit }} characters"
     * )
     */
    public string $shortName;

    /**
     * @var string
     * @Assert\NotBlank(message="Full name is required")
     * @Assert\Length(
     *     min="5",
     *     minMessage = "Full name length must be at least {{ limit }} characters long"
     * )
     */
    public string $fullName;

    /**
     * @var string
     * @Assert\NotBlank(message="Country is required")
     * @Assert\Length(
     *     min="3",
     *     minMessage = "Country length must be at least {{ limit }} characters long"
     * )
     */
    public string $country;
}
