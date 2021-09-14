<?php

declare(strict_types=1);

namespace App\Model\Income;

use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class CreateIncomeSchema extends AbstractApiSchema
{
    /**
     * @var string
     * @Assert\NotBlank(message="Document date is required")
     */
    public string $date;

    /**
     * @var string
     * @Assert\NotBlank(message="Supplier is required")
     */
    public string $supplierId;

    /**
     * @var string
     * @Assert\NotBlank(message="Store is required")
     */
    public string $storeId;

    /**
     * @var array
     */
    public array $rows;

    public function __construct()
    {
        $this->rows = [];
    }
}
