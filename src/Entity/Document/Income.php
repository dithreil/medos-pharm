<?php

declare(strict_types=1);

namespace App\Entity\Document;

use App\Entity\Store;
use App\Entity\Supplier;
use App\Exception\AppException;
use App\Repository\Document\IncomeRepository;
use App\Utils\DateTimeUtils;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=IncomeRepository::class)
 * @ORM\Table(name="incomes")
 */
class Income
{
    /**
     * @var string
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id()
     */
    private string $id;

    /**
     * @var float|null
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private ?float $amount;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="date", type="datetime_immutable")
     */
    private \DateTimeImmutable $date;

    /**
     * @var Supplier
     * @ORM\ManyToOne(targetEntity=Supplier::class, inversedBy="incomes")
     */
    private Supplier $supplier;

    /**
     * @var Store
     * @ORM\ManyToOne(targetEntity=Store::class, inversedBy="incomes")
     */
    private Store $store;

    /**
     * @var StockDocument
     * @ORM\OneToOne(targetEntity="StockDocument", inversedBy="income")
     * @ORM\JoinColumn(name="stock_document_id", referencedColumnName="id", nullable=false)
     */
    private StockDocument $stockDocument;

    /**
     * @var PriceDocument
     * @ORM\OneToOne(targetEntity="PriceDocument", inversedBy="income")
     * @ORM\JoinColumn(name="price_document_id", referencedColumnName="id", nullable=false)
     */
    private PriceDocument $priceDocument;

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
     * @var bool
     */
    private bool $isSet;

    /**
     * @param Supplier $supplier
     * @param Store $store
     * @param StockDocument $stockDocument
     * @param PriceDocument $priceDocument
     * @param \DateTimeImmutable $date
     * @param bool $isSet
     * @throws AppException
     */
    public function __construct(
        Supplier $supplier,
        Store $store,
        StockDocument $stockDocument,
        PriceDocument $priceDocument,
        \DateTimeImmutable $date,
        bool $isSet = false
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->supplier = $supplier;
        $this->store = $store;
        $this->stockDocument = $stockDocument;
        $this->priceDocument = $priceDocument;
        $this->date = $date;
        $this->isSet = $isSet;
        $this->amount = null;

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
     * @return Supplier
     */
    public function getSupplier(): Supplier
    {
        return $this->supplier;
    }

    /**
     * @param Supplier $supplier
     */
    public function setSupplier(Supplier $supplier): void
    {
        $this->supplier = $supplier;
    }

    /**
     * @return Store
     */
    public function getStore(): Store
    {
        return $this->store;
    }

    /**
     * @param Store $store
     */
    public function setStore(Store $store): void
    {
        $this->store = $store;
    }

    /**
     * @return StockDocument
     */
    public function getStockDocument(): StockDocument
    {
        return $this->stockDocument;
    }

    /**
     * @param StockDocument $stockDocument
     */
    public function setStockDocument(StockDocument $stockDocument): void
    {
        $this->stockDocument = $stockDocument;
    }

    /**
     * @return PriceDocument
     */
    public function getPriceDocument(): PriceDocument
    {
        return $this->priceDocument;
    }

    /**
     * @param PriceDocument $priceDocument
     */
    public function setPriceDocument(PriceDocument $priceDocument): void
    {
        $this->priceDocument = $priceDocument;
    }

    /**
     * @return float|null
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @param float|null $amount
     */
    public function setAmount(?float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return \DateTimeImmutable     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @param \DateTimeImmutable $date     */
    public function setDate(\DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    /**
     * @return bool     */
    public function isSet(): bool
    {
        return $this->isSet;
    }

    /**
     * @param bool $isSet     */
    public function setIsSet(bool $isSet): void
    {
        $this->isSet = $isSet;
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
