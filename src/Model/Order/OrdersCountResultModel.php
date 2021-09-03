<?php

declare(strict_types=1);

namespace App\Model\Order;

class OrdersCountResultModel
{
    /**
     * @var int
     */
    public int $all;

    /**
     * @var int
     */
    public int $notPaid;

    /**
     * @var int
     */
    public int $paid;

    /**
     * OrdersCountResultModel constructor.
     * @param int $all
     * @param int $notPaid
     * @param int $paid
     */
    public function __construct(int $all, int $notPaid, int $paid)
    {
        $this->all = $all;
        $this->notPaid = $notPaid;
        $this->paid = $paid;
    }
}
