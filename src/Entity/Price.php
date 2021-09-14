<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\AppException;
use App\Repository\PriceRepository;
use App\Utils\DateTimeUtils;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=PriceRepository::class)
 * @ORM\Table(name="prices")
 */
class Price
{
    /**
     * @var string
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id()
     */
    private string $id;

    /**
     * @var float
     * @ORM\Column(name="value", type="float")
     */
    private float $value;

    /**
     * @var Characteristic
     * @ORM\ManyToOne(targetEntity=Characteristic::class, inversedBy="prices")
     */
    private Characteristic $characteristic;

    /**
     * @var Store
     * @ORM\ManyToOne(targetEntity=Store::class, inversedBy="prices")
     */
    private Store $store;

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
     * @var \DateTimeImmutable|null
     * @ORM\Column(name="delete_time", type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $deleteTime;

    /**
     * @param Characteristic $characteristic
     * @param Store $store
     * @param float $value
     * @throws AppException
     */
    public function __construct(Characteristic $characteristic, Store $store, float $value)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->characteristic = $characteristic;
        $this->store = $store;
        $this->value = $value;

        $date = DateTimeUtils::now();
        $this->createTime = $date;
        $this->updateTime = $date;
        $this->deleteTime = null;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
     * @return \DateTimeImmutable|null
     */
    public function getDeleteTime(): ?\DateTimeImmutable
    {
        return $this->deleteTime;
    }

    /**
     * @param \DateTimeImmutable $deleteTime
     * @throws AppException
     */
    public function setDeleteTime(\DateTimeImmutable $deleteTime): void
    {
        if ($this->deleteTime !== null) {
            throw new AppException("This entity already removed!");
        }
        $this->deleteTime = $deleteTime;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdateTime(): \DateTimeImmutable
    {
        return $this->updateTime;
    }
}
