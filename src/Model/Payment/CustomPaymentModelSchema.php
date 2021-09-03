<?php

declare(strict_types=1);

namespace App\Model\Payment;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class CustomPaymentModelSchema extends AbstractApiSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Order is required")
     */
    public string $order;
}
