<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\AppException;
use App\Repository\PaymentRepository;
use App\Utils\DateTimeUtils;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 * @ORM\Table(name="payments")
 */
class Payment
{
    /**
     * @var string
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id()
     */
    private string $id;

    /**
     * @var float
     * @ORM\Column(name="amount", type="float")
     */
    private float $amount;

    /**
     * @var string
     * @ORM\Column(name="status", type="string")
     */
    private string $status;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="payments")
     */
    private Order $order;

    /**
     * @var string
     * @ORM\Column(name="employee_name", type="string")
     */
    private string $employeeName;

    /**
     * @var string
     * @ORM\Column(name="client_name", type="string")
     */
    private string $clientName;

    /**
     * @var string|null
     * @ORM\Column(name="payment_key", type="string", nullable=true)
     */
    private ?string $paymentKey;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="create_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $createTime;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="update_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $updateTime;

    /**
     * @param Order $order
     * @param float $amount
     * @param string $status
     * @param string|null $paymentKey
     * @throws AppException
     */
    public function __construct(Order $order, float $amount, string $status, ?string $paymentKey = null)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->amount = $amount;
        $this->status = $status;
        $this->order = $order;
        $this->paymentKey = $paymentKey;
        $this->employeeName = $order->getEmployee()->getFullName();
        $this->clientName = $order->getClient()->getFullName();
        $this->setOrder($order);

        $date = DateTimeUtils::now();
        $this->createTime = $date;
        $this->updateTime = $date;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): void
    {
        if ($this->order === $order) {
            return;
        }

        $this->order = $order;
        $order->addPayment($this);
    }

    /**
     * @return string
     */
    public function getEmployeeName(): string
    {
        return $this->employeeName;
    }

    /**
     * @param string $employeeName
     */
    public function setEmployeeName(string $employeeName): void
    {
        $this->employeeName = $employeeName;
    }

    /**
     * @return string
     */
    public function getClientName(): string
    {
        return $this->clientName;
    }

    /**
     * @param string $clientName
     */
    public function setClientName(string $clientName): void
    {
        $this->clientName = $clientName;
    }

    /**
     * @return string|null
     */
    public function getPaymentKey(): ?string
    {
        return $this->paymentKey;
    }

    /**
     * @param string|null $paymentKey
     */
    public function setPaymentKey(?string $paymentKey): void
    {
        $this->paymentKey = $paymentKey;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate(): void
    {
        $this->updateTime = new \DateTimeImmutable('now');
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreateTime(): \DateTimeImmutable
    {
        return $this->createTime;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdateTime(): \DateTimeImmutable
    {
        return $this->updateTime;
    }
}
