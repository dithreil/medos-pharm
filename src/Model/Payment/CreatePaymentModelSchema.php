<?php

declare(strict_types=1);

namespace App\Model\Payment;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePaymentModelSchema extends AbstractApiSchema
{
    /**
     * @var float
     * @Assert\NotBlank(message="Amount is required", groups={"create"})
     */
    public float $amount;

    /**
     * @var string
     * @Assert\NotBlank(message="Order is required", groups={"create"})
     */
    public string $order;

    /**
     * @var string
     * @Assert\NotBlank(message="Status is required", groups={"edit"})
     */
    public string $status;
}
