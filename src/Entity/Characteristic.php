<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Change\PriceChange;
use App\Entity\Change\StockChange;
use App\Exception\AppException;
use App\Repository\CharacteristicRepository;
use App\Utils\DateTimeUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=CharacteristicRepository::class)
 * @ORM\Table(name="characteristics")
 */
class Characteristic
{
    /**
     * @var string
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id()
     */
    private string $id;

    /**
     * @var string
     * @ORM\Column(name="serial", type="string", length=80)
     */
    private string $serial;

    /**
     * @var int
     * @ORM\Column(name="butch", type="integer")
     */
    private int $butch;

    /**
     * @var Nomenclature
     * @ORM\ManyToOne(targetEntity=Nomenclature::class, inversedBy="characteristics")
     */
    private Nomenclature $nomenclature;

    /**
     * @var Collection|Price[]
     * @ORM\OneToMany(targetEntity=Price::class, mappedBy="characteristic")
     */
    private Collection $prices;

    /**
     * @var Collection|StockChange[]
     * @ORM\OneToMany(targetEntity=StockChange::class, mappedBy="characteristic")
     */
    private Collection $stockChanges;

    /**
     * @var Collection|PriceChange[]
     * @ORM\OneToMany(targetEntity=PriceChange::class, mappedBy="characteristic")
     */
    private Collection $priceChanges;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="expire_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $expireTime;

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
     * @param Nomenclature $nomenclature
     * @param string $serial
     * @param int $butch
     * @param \DateTimeImmutable $expireTime
     * @throws AppException
     */
    public function __construct(
        Nomenclature $nomenclature,
        string $serial,
        int $butch,
        \DateTimeImmutable $expireTime
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->nomenclature = $nomenclature;
        $this->serial = $serial;
        $this->butch = $butch;
        $this->expireTime = $expireTime;

        $this->prices = new ArrayCollection();
        $this->stockChanges = new ArrayCollection();
        $this->priceChanges = new ArrayCollection();

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
     * @return Nomenclature
     */
    public function getNomenclature(): Nomenclature
    {
        return $this->nomenclature;
    }

    /**
     * @param Nomenclature $nomenclature
     */
    public function setNomenclature(Nomenclature $nomenclature): void
    {
        $this->nomenclature = $nomenclature;
    }

    /**
     * @return string
     */
    public function getSerial(): string
    {
        return $this->serial;
    }

    /**
     * @param string $serial
     */
    public function setSerial(string $serial): void
    {
        $this->serial = $serial;
    }

    /**
     * @return int
     */
    public function getButch(): int
    {
        return $this->butch;
    }

    /**
     * @param int $butch
     */
    public function setButch(int $butch): void
    {
        $this->butch = $butch;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpireTime(): \DateTimeImmutable
    {
        return $this->expireTime;
    }

    /**
     * @param \DateTimeImmutable $expireTime
     */
    public function setExpireTime(\DateTimeImmutable $expireTime): void
    {
        $this->expireTime = $expireTime;
    }

    /**
     * @return Collection|Price[]
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    /**
     * @param Price $price
     */
    public function addPrice(Price $price): void
    {
        if ($this->prices->contains($price)) {
            return;
        }

        $this->prices->add($price);
        $price->setCharacteristic($this);
    }

    /**
     * @param Price $price
     */
    public function removePrice(Price $price): void
    {
        if (!$this->prices->contains($price)) {
            return;
        }

        $this->prices->removeElement($price);
    }

    /**
     * @return Collection|StockChange[]
     */
    public function getStockChanges(): Collection
    {
        return $this->stockChanges;
    }

    /**
     * @param StockChange $stockChange
     */
    public function addStockChange(StockChange $stockChange): void
    {
        if ($this->stockChanges->contains($stockChange)) {
            return;
        }

        $this->stockChanges->add($stockChange);
        $stockChange->setCharacteristic($this);
    }

    /**
     * @param StockChange $stockChange
     */
    public function removeStockChange(StockChange $stockChange): void
    {
        if (!$this->stockChanges->contains($stockChange)) {
            return;
        }

        $this->stockChanges->removeElement($stockChange);
    }

    /**
     * @return Collection|PriceChange[]
     */
    public function getPriceChanges(): Collection
    {
        return $this->priceChanges;
    }

    /**
     * @param PriceChange $priceChange
     */
    public function addPriceChange(PriceChange $priceChange): void
    {
        if ($this->priceChanges->contains($priceChange)) {
            return;
        }

        $this->priceChanges->add($priceChange);
        $priceChange->setCharacteristic($this);
    }

    /**
     * @param PriceChange $priceChange
     */
    public function removePriceChange(PriceChange $priceChange): void
    {
        if (!$this->priceChanges->contains($priceChange)) {
            return;
        }

        $this->priceChanges->removeElement($priceChange);
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
