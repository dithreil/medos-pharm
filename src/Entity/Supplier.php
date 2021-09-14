<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Document\Income;
use App\Entity\Document\StockDocument;
use App\Exception\AppException;
use App\Repository\SupplierRepository;
use App\Utils\DateTimeUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=SupplierRepository::class)
 * @ORM\Table(name="suppliers")
 */
class Supplier
{
    /**
     * @var string
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id()
     */
    private string $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private string $name;

    /**
     * @var string
     * @ORM\Column(name="address", type="string")
     */
    private string $address;

    /**
     * @var string|null
     * @ORM\Column(name="email", type="string")
     */
    private ?string $email;

    /**
     * @var string|null
     * @ORM\Column(name="phone_number", type="string", length=11)
     */
    private ?string $phoneNumber;

    /**
     * @var string|null
     * @ORM\Column(name="information", type="string")
     */
    private ?string $information;

    /**
     * @var Collection|Income[]
     * @ORM\OneToMany(targetEntity=StockDocument::class, mappedBy="supplier")
     */
    private Collection $incomes;

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
     * @param string $name
     * @param string $address
     * @param string|null $email
     * @param string|null $phoneNumber
     * @param string|null $information
     * @throws AppException
     */
    public function __construct(
        string $name,
        string $address,
        ?string $email,
        ?string $phoneNumber,
        ?string $information
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->name = $name;
        $this->address = $address;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->information = $information;

        $this->incomes = new ArrayCollection();

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     */
    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string|null
     */
    public function getInformation(): ?string
    {
        return $this->information;
    }

    /**
     * @param string|null $information
     */
    public function setInformation(?string $information): void
    {
        $this->information = $information;
    }

    /**
     * @return Collection|Income[]
     */
    public function getIncomes(): Collection
    {
        return $this->incomes;
    }

    /**
     * @param Income $incomes
     */
    public function addStockIncome(Income $incomes): void
    {
        if ($this->incomes->contains($incomes)) {
            return;
        }

        $this->incomes->add($incomes);
        $incomes->setSupplier($this);
    }

    /**
     * @param Income $incomes
     */
    public function removeIncome(Income $incomes): void
    {
        if (!$this->incomes->contains($incomes)) {
            return;
        }

        $this->incomes->removeElement($incomes);
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
}
