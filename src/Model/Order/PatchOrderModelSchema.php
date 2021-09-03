<?php

declare(strict_types=1);

namespace App\Model\Order;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class PatchOrderModelSchema extends AbstractApiSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Action is required")
     */
    public string $action;

    /**
     * @var int|null
     * @Assert\Range(
     *      min = 1,
     *      max = 5,
     *      notInRangeMessage = "Rating must be between {{ min }} and {{ max }}",
     * )
     */
    public ?int $rating;

    /**
     * @var string|null
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Comment length is limited to {{ limit }} characters"
     * )
     */
    public ?string $ratingComment;
}
