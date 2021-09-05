<?php

declare(strict_types=1);

namespace App\Entity;

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
     * @ORM\Column(name="delete_time", type="datetime_immutable")
     */
    private ?\DateTimeImmutable $deleteTime;

    /**
     * @param Nomenclature $nomenclature
     * @param string $serial
     * @param \DateTimeImmutable $expireTime
     * @throws AppException
     */
    public function __construct(Nomenclature $nomenclature, string $serial, \DateTimeImmutable $expireTime)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->nomenclature = $nomenclature;
        $this->serial = $serial;
        $this->expireTime = $expireTime;

        $this->prices = new ArrayCollection();

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
