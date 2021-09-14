<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Document\Income;
use App\Entity\Document\PriceDocument;
use App\Entity\Document\StockDocument;
use App\Exception\AppException;
use App\Repository\StoreRepository;
use App\Utils\DateTimeUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=StoreRepository::class)
 * @ORM\Table(name="stores")
 */
class Store
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
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private ?string $description;

    /**
     * @var Collection|Employee[]
     * @ORM\OneToMany(targetEntity=Employee::class, mappedBy="store")
     */
    private Collection $employees;

    /**
     * @var Collection|Income[]
     * @ORM\OneToMany(targetEntity=Income::class, mappedBy="store")
     */
    private Collection $incomes;

    /**
     * @var Collection|StockDocument[]
     * @ORM\OneToMany(targetEntity=StockDocument::class, mappedBy="store")
     */
    private Collection $stockDocuments;

    /**
     * @var Collection|PriceDocument[]
     * @ORM\OneToMany(targetEntity=PriceDocument::class, mappedBy="store")
     */
    private Collection $priceDocuments;

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
     * @param string|null $description
     * @throws AppException
     */
    public function __construct(string $name, string $address, ?string $description)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->name = $name;
        $this->address = $address;
        $this->description = $description;

        $this->employees = new ArrayCollection();
        $this->stockDocuments = new ArrayCollection();
        $this->incomes = new ArrayCollection();
        $this->priceDocuments = new ArrayCollection();

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
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection|Employee[]
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    /**
     * @param Employee $employee
     */
    public function addEmployee(Employee $employee): void
    {
        if ($this->employees->contains($employee)) {
            return;
        }

        $this->employees->add($employee);
        $employee->setStore($this);
    }

    /**
     * @param Employee $employee
     */
    public function removeEmployee(Employee $employee): void
    {
        if (!$this->employees->contains($employee)) {
            return;
        }

        $this->employees->removeElement($employee);
    }

    /**
     * @return Collection|Income[]
     */
    public function getIncomes(): Collection
    {
        return $this->incomes;
    }

    /**
     * @param Income $income
     */
    public function addIncome(Income $income): void
    {
        if ($this->incomes->contains($income)) {
            return;
        }

        $this->incomes->add($income);
        $income->setStore($this);
    }

    /**
     * @param Income $income
     */
    public function removeIncome(Income $income): void
    {
        if (!$this->incomes->contains($income)) {
            return;
        }

        $this->incomes->removeElement($income);
    }

    /**
     * @return Collection|StockDocument[]
     */
    public function getStockDocuments(): Collection
    {
        return $this->stockDocuments;
    }

    /**
     * @param StockDocument $stockDocument
     */
    public function addStockDocument(StockDocument $stockDocument): void
    {
        if ($this->stockDocuments->contains($stockDocument)) {
            return;
        }

        $this->stockDocuments->add($stockDocument);
        $stockDocument->setStore($this);
    }

    /**
     * @param StockDocument $stockDocument
     */
    public function removeStockDocument(StockDocument $stockDocument): void
    {
        if (!$this->stockDocuments->contains($stockDocument)) {
            return;
        }

        $this->stockDocuments->removeElement($stockDocument);
    }

    /**
     * @return Collection|PriceDocument[]
     */
    public function getPriceDocuments(): Collection
    {
        return $this->priceDocuments;
    }

    /**
     * @param PriceDocument $priceDocument
     */
    public function addPriceDocument(PriceDocument $priceDocument): void
    {
        if ($this->priceDocuments->contains($priceDocument)) {
            return;
        }

        $this->priceDocuments->add($priceDocument);
        $priceDocument->setStore($this);
    }

    /**
     * @param PriceDocument $priceDocument
     */
    public function removePriceDocument(PriceDocument $priceDocument): void
    {
        if (!$this->priceDocuments->contains($priceDocument)) {
            return;
        }

        $this->priceDocuments->removeElement($priceDocument);
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
