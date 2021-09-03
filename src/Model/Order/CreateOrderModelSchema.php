<?php

declare(strict_types=1);

namespace App\Model\Order;

use App\DataProvider\CategoryDataProvider;
use App\DataProvider\OrderDataProvider;
use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderModelSchema extends EditOrderModelSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Client is required", groups={"admin-order"})
     */
    public string $client;

    /**
     * @var string|null
     */
    public ?string $category;

    /**
     * CreateOrderModelSchema constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->category = CategoryDataProvider::PAID_SERVICES;
    }
}
