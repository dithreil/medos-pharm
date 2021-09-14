<?php

declare(strict_types=1);

namespace App\Entity\Change;

use App\Entity\Characteristic;
use App\Entity\Document\PriceDocument;
use App\Exception\AppException;
use App\Repository\Change\PriceChangeRepository;
use App\Utils\DateTimeUtils;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=PriceChangeRepository::class)
 * @ORM\Table(name="price_changes")
 */
class PriceChange
{
    /**
     * @var string
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id()
     */
    private string $id;

    /**
     * @var Characteristic
     * @ORM\ManyToOne(targetEntity=Characteristic::class, inversedBy="priceChanges")
     */
    private Characteristic $characteristic;

    /**
     * @var PriceDocument
     * @ORM\ManyToOne(targetEntity=PriceDocument::class, inversedBy="priceChanges")
     */
    private PriceDocument $document;

    /**
     * @var float
     * @ORM\Column(name="old_value", type="float")
     */
    private float $oldValue;

    /**
     * @var float
     * @ORM\Column(name="new_value", type="float")
     */
    private float $newValue;

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
     * @param PriceDocument $document
     * @param Characteristic $characteristic
     * @param float $oldValue
     * @param float $newValue
     * @param bool $isSet
     * @throws AppException
     */
    public function __construct(
        PriceDocument $document,
        Characteristic $characteristic,
        float $oldValue,
        float $newValue,
        bool $isSet = false
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->document = $document;
        $this->characteristic = $characteristic;
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
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
     * @return PriceDocument
     */
    public function getDocument(): PriceDocument
    {
        return $this->document;
    }

    /**
     * @param PriceDocument $document
     */
    public function setDocument(PriceDocument $document): void
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
     * @return float
     */
    public function getOldValue(): float
    {
        return $this->oldValue;
    }

    /**
     * @param float $oldValue
     */
    public function setOldValue(float $oldValue): void
    {
        $this->oldValue = $oldValue;
    }

    /**
     * @return float
     */
    public function getNewValue(): float
    {
        return $this->newValue;
    }

    /**
     * @param float $newValue
     */
    public function setNewValue(float $newValue): void
    {
        $this->newValue = $newValue;
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
