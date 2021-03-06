<?php

declare(strict_types=1);

namespace App\Entity\Change;

use App\Entity\Characteristic;
use App\Entity\Document\StockDocument;
use App\Exception\AppException;
use App\Repository\Change\StockChangeRepository;
use App\Utils\DateTimeUtils;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=StockChangeRepository::class)
 * @ORM\Table(name="stock_changes")
 */
class StockChange
{
    /**
     * @var string
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id()
     */
    private string $id;

    /**
     * @var Characteristic
     * @ORM\ManyToOne(targetEntity=Characteristic::class, inversedBy="stockChanges")
     */
    private Characteristic $characteristic;

    /**
     * @var StockDocument
     * @ORM\ManyToOne(targetEntity=StockDocument::class, inversedBy="stockChanges")
     */
    private StockDocument $document;

    /**
     * @var PriceChange
     * @ORM\OneToOne(targetEntity="PriceChange", inversedBy="stockChange")
     * @ORM\JoinColumn(name="price_change_id", referencedColumnName="id", nullable=false)
     */
    private PriceChange $priceChange;

    /**
     * @var float
     * @ORM\Column(name="value", type="float")
     */
    private float $value;

    /**
     * @var bool
     * @ORM\Column(name="is_set", type="boolean")
     */
    private bool $isSet;

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
     * @param StockDocument $document
     * @param Characteristic $characteristic
     * @param float $value
     * @param PriceChange $priceChange
     * @param bool $isSet
     * @throws AppException
     */
    public function __construct(
        StockDocument $document,
        Characteristic $characteristic,
        float $value,
        PriceChange $priceChange,
        bool $isSet = false
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->document = $document;
        $this->characteristic = $characteristic;
        $this->value = $value;
        $this->priceChange = $priceChange;
        $this->isSet = $isSet;

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
     * @return StockDocument
     */
    public function getDocument(): StockDocument
    {
        return $this->document;
    }

    /**
     * @param StockDocument $document
     */
    public function setDocument(StockDocument $document): void
    {
        $this->document = $document;
    }

    /**
     * @return Characteristic
     */
    public function getCharacteristic(): Characteristic
    {
        return $this->characteristic;
    }

    /**
     * @param Characteristic $characteristic
     */
    public function setCharacteristic(Characteristic $characteristic): void
    {
        $this->characteristic = $characteristic;
    }

    /**
     * @return PriceChange
     */
    public function getPriceChange(): PriceChange
    {
        return $this->priceChange;
    }

    /**
     * @param PriceChange $priceChange
     */
    public function setPriceChange(PriceChange $priceChange): void
    {
        $this->priceChange = $priceChange;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue(float $value): void
    {
        $this->value = $value;
    }

    /**
     * @param string $nomenclature
     * @param string $serial
     * @param string $expire
     * @param float $purchasePrice
     * @param float $retailPrice
     * @param float $value
     * @return bool
     * @throws AppException
     */
    public function isEqualToNewRow(
        string $nomenclature,
        string $serial,
        string $expire,
        float $purchasePrice,
        float $retailPrice,
        float $value
    ): bool {
        $expireTime = DateTimeUtils::parse($expire);
        return $this->getCharacteristic()->getNomenclature()->getId() === $nomenclature
            && $this->getCharacteristic()->getSerial() === $serial
            && $this->getPriceChange()->getOldValue() === $purchasePrice
            && $this->getPriceChange()->getNewValue() === $retailPrice
            && $this->getValue() === $value;
    }

    /**
     * @return bool
     */
    public function isSet(): bool
    {
        return $this->isSet;
    }

    /**
     * @param bool $isSet
     */
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
