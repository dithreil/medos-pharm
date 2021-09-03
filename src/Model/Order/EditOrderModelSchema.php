<?php

declare(strict_types=1);

namespace App\Model\Order;

use App\DataProvider\OrderDataProvider;
use App\Model\AbstractApiSchema;
use Symfony\Component\Validator\Constraints as Assert;

class EditOrderModelSchema extends AbstractApiSchema
{
    /**
     * @var float
     * @Assert\NotBlank(message="Cost is required", groups={"client-order", "admin-order"})
     */
    public float $cost;

    /**
     * @var string|null
     */
    public ?string $type;

    /**
     * @var int
     * @Assert\NotBlank(message="Duration is required", groups={"client-order"})
     */
    public int $duration;

    /**
     * @var string
     * @Assert\NotBlank(message="Start time is required", groups={"client-order", "admin-order"})
     */
    public string $startTime;

    /**
     * @var string
     * @Assert\NotBlank(message="Employee is required", groups={"client-order", "admin-order"})
     */
    public string $employee;

    /**
     * @var string|null
     */
    public ?string $status;

    /**
     * @var string|null
     */
    public ?string $communication;

    /**
     * @var string|null
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Username length is limited to {{ limit }} characters"
     * )
     */
    public ?string $clientTarget;

    /**
     * @var string|null
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Comment length is limited to {{ limit }} characters"
     * )
     */
    public ?string $clientComment;

    /**
     * @var string|null
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Comment length is limited to {{ limit }} characters"
     * )
     */
    public ?string $employeeComment;

    public function __construct()
    {
        $this->type = OrderDataProvider::TYPE_ONLINE;
        $this->status = null;
        $this->communication = null;
        $this->clientTarget = null;
        $this->clientComment = null;
        $this->employeeComment = null;
    }
}
