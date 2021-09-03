<?php

declare(strict_types=1);

namespace App\Entity;

use App\DataProvider\OrderDataProvider;
use App\Exception\AppException;
use App\Repository\OrderRepository;
use App\Utils\DateTimeUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="orders")
 */
class Order
{
    /**
     * @var string
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id()
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="orders")
     */
    private Client $client;

    /**
     * @ORM\ManyToOne(targetEntity=Employee::class, inversedBy="orders")
     */
    private Employee $employee;

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
     * @var \DateTimeImmutable
     * @ORM\Column(name="start_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $startTime;

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(name="payment_time", type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $paymentTime;

    /**
     * @var float
     * @ORM\Column(name="cost", type="float")
     */
    private float $cost;

    /**
     * @var string
     * @ORM\Column(name="status", type="string")
     */
    private string $status;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=1)
     */
    private string $type;

    /**
     * @var int
     * @ORM\Column(name="duration", type="integer")
     */
    private int $duration;

    /**
     * @var int|null
     * @ORM\Column(name="code", type="integer", nullable=true)
     */
    private ?int $code;

    /**
     * @var int|null
     * @ORM\Column(name="rating", type="integer", nullable=true)
     */
    private ?int $rating;

    /**
     * @var string|null
     * @ORM\Column(name="communication", type="string", length=30, nullable=true)
     */
    private ?string $communication;

    /**
     * @var string|null
     * @ORM\Column(name="client_target", type="string", length=100, nullable=true)
     */
    private ?string $clientTarget;

    /**
     * @var string|null
     * @ORM\Column(name="employee_comment", type="string", length=255, nullable=true)
     */
    private ?string $employeeComment;

    /**
     * @var string|null
     * @ORM\Column(name="client_comment", type="string", length=255, nullable=true)
     */
    private ?string $clientComment;

    /**
     * @var string|null
     * @ORM\Column(name="rating_comment", type="string", length=255, nullable=true)
     */
    private ?string $ratingComment;

    /**
     * @ORM\OneToMany(targetEntity=Payment::class, mappedBy="order")
     */
    private Collection $payments;

    /**
     * @param Client $client
     * @param Employee $employee
     * @param \DateTimeImmutable $startTime
     * @param float $cost
     * @param int $duration
     * @param string $type
     * @param string $status
     * @param string|null $communication
     * @param string|null $clientTarget
     * @param string|null $clientComment
     * @param string|null $employeeComment
     * @throws AppException
     */
    public function __construct(
        Client $client,
        Employee $employee,
        \DateTimeImmutable $startTime,
        float $cost,
        int $duration,
        string $type,
        string $status,
        ?string $communication = null,
        ?string $clientTarget = null,
        ?string $clientComment = null,
        ?string $employeeComment = null
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->cost = $cost;
        $this->duration = $duration;
        $this->status = $status;
        $this->type = $type;
        $this->communication = $communication;
        $this->clientTarget = $clientTarget;
        $this->clientComment = $clientComment;
        $this->employeeComment = $employeeComment;
        $this->startTime = $startTime;
        $this->client = $client;
        $client->addOrder($this);
        $this->employee = $employee;
        $employee->addOrder($this);
        $this->rating = null;
        $this->ratingComment = null;
        $this->code = null;
        $this->paymentTime = null;

        $this->payments = new ArrayCollection();

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
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        if ($this->client === $client) {
            return;
        }

        $this->client = $client;
        $client->addOrder($this);
    }

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     */
    public function setEmployee(Employee $employee): void
    {
        if ($this->employee === $employee) {
            return;
        }

        $this->employee = $employee;
        $employee->addOrder($this);
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getStartTime(): \DateTimeImmutable
    {
        return $this->startTime;
    }

    /**
     * @param \DateTimeImmutable $startTime
     */
    public function setStartTime(\DateTimeImmutable $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getPaymentTime(): ?\DateTimeImmutable
    {
        return $this->paymentTime;
    }

    /**
     * @param \DateTimeImmutable|null $paymentTime
     */
    public function setPaymentTime(?\DateTimeImmutable $paymentTime): void
    {
        $this->paymentTime = $paymentTime;
    }

    /**
     * @ORM\PreUpdate()
     * @throws AppException
     */
    public function onPreUpdate(): void
    {
        $this->updateTime = DateTimeUtils::now();
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

    /**
     * @return float
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * @param float $cost
     */
    public function setCost(float $cost): void
    {
        $this->cost = $cost;
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
     * @throws AppException
     */
    public function setStatus(string $status): void
    {
        if (!OrderDataProvider::isStatusAllowed($status)) {
            throw new AppException("Selected order status is not allowed!", Response::HTTP_BAD_REQUEST);
        }
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @throws AppException
     */
    public function setType(string $type): void
    {
        if (!OrderDataProvider::isTypeAllowed($type)) {
            throw new AppException("Selected order type is not allowed!", Response::HTTP_BAD_REQUEST);
        }
        $this->type = $type;
    }

    /**
     * @return int|null
     */
    public function getCode(): ?int
    {
        return $this->code;
    }

    /**
     * @param int|null $code
     */
    public function setCode(?int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return int|null
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     * @throws AppException
     */
    public function setRating(int $rating): void
    {
        if (!OrderDataProvider::isRatingAllowed($rating)) {
            throw new AppException("Selected order rating is not allowed!", Response::HTTP_BAD_REQUEST);
        }
        $this->rating = $rating;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return string|null
     */
    public function getClientComment(): ?string
    {
        return $this->clientComment;
    }

    /**
     * @param string|null $clientComment
     */
    public function setClientComment(?string $clientComment): void
    {
        $this->clientComment = $clientComment;
    }

    /**
     * @return string|null
     */
    public function getRatingComment(): ?string
    {
        return $this->ratingComment;
    }

    /**
     * @param string|null $ratingComment
     */
    public function setRatingComment(?string $ratingComment): void
    {
        $this->ratingComment = $ratingComment;
    }

    /**
     * @return string|null
     */
    public function getEmployeeComment(): ?string
    {
        return $this->employeeComment;
    }

    /**
     * @param string|null $employeeComment
     */
    public function setEmployeeComment(?string $employeeComment): void
    {
        $this->employeeComment = $employeeComment;
    }

    /**
     * @return string|null
     */
    public function getCommunication(): ?string
    {
        return $this->communication;
    }

    /**
     * @param string|null $communication
     * @throws AppException
     */
    public function setCommunication(?string $communication): void
    {
        if (!OrderDataProvider::isCommunicationAllowed($communication)) {
            throw new AppException("Selected communication is not allowed!", Response::HTTP_BAD_REQUEST);
        }
        $this->communication = $communication;
    }

    /**
     * @return string|null
     */
    public function getClientTarget(): ?string
    {
        return $this->clientTarget;
    }

    /**
     * @param string|null $clientTarget
     */
    public function setClientTarget(?string $clientTarget): void
    {
        $this->clientTarget = $clientTarget;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    /**
     * @param Payment $payment
     */
    public function addPayment(Payment $payment): void
    {
        if ($this->payments->contains($payment)) {
            return;
        }

        $this->payments->add($payment);
        $payment->setOrder($this);
    }

    /**
     * @param Payment $payment
     */
    public function removePayment(Payment $payment): void
    {
        if (!$this->payments->contains($payment)) {
            return;
        }

        $this->payments->removeElement($payment);
    }

}
