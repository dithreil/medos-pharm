<?php

declare(strict_types=1);

namespace App\Entity;

use App\DataProvider\NomenclatureDataProvider;
use App\Exception\AppException;
use App\Repository\NomenclatureRepository;
use App\Utils\DateTimeUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=NomenclatureRepository::class)
 * @ORM\Table(name="nomenclatures")
 */
class Nomenclature
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
     * @var int
     * @ORM\Column(name="price_type", type="smallint")
     */
    private int $priceType;

    /**
     * @var int
     * @ORM\Column(name="medical_form", type="smallint")
     */
    private int $medicalForm;

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
     * @var Producer
     * @ORM\ManyToOne(targetEntity=Producer::class, inversedBy="nomenclatures")
     */
    private Producer $producer;

    /**
     * @var Collection|Characteristic[]
     * @ORM\OneToMany(targetEntity=Characteristic::class, mappedBy="nomenclature")
     */
    private Collection $characteristics;

    /**
     * @param Producer $producer
     * @param string $name
     * @param int $medicalForm
     * @param int $priceType
     * @throws AppException
     */
    public function __construct(
        Producer $producer,
        string $name,
        int $medicalForm,
        int $priceType = NomenclatureDataProvider::PRICE_TYPE_NORMAL
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->producer = $producer;
        $this->name = $name;
        $this->medicalForm = $medicalForm;
        $this->priceType = $priceType;

        $this->characteristics = new ArrayCollection();

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
     * @return Producer
     */
    public function getProducer(): Producer
    {
        return $this->producer;
    }

    /**
     * @param Producer $producer
     */
    public function setProducer(Producer $producer): void
    {
        $this->producer = $producer;
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
     * @return int
     */
    public function getMedicalForm(): int
    {
        return $this->medicalForm;
    }

    /**
     * @param int $medicalForm
     * @throws AppException
     */
    public function setMedicalForm(int $medicalForm): void
    {
        if (!NomenclatureDataProvider::isMedicalFormAllowed($medicalForm)) {
            throw new AppException('Medical form is not allowed!');
        }
        $this->medicalForm = $medicalForm;
    }

    /**
     * @return int
     */
    public function getPriceType(): int
    {
        return $this->priceType;
    }

    /**
     * @param int $priceType
     * @throws AppException
     */
    public function setPriceType(int $priceType): void
    {
        if (!NomenclatureDataProvider::isPriceTypeAllowed($priceType)) {
            throw new AppException('Price type is not allowed!');
        }
        $this->priceType = $priceType;
    }

    /**
     * @return Collection|Characteristic[]
     */
    public function getCharacteristics(): Collection
    {
        return $this->characteristics;
    }

    /**
     * @param Characteristic $characteristic
     */
    public function addCharacteristic(Characteristic $characteristic): void
    {
        if ($this->characteristics->contains($characteristic)) {
            return;
        }

        $this->characteristics->add($characteristic);
        $characteristic->setNomenclature($this);
    }

    /**
     * @param Characteristic $characteristic
     */
    public function removeCharacteristic(Characteristic $characteristic): void
    {
        if (!$this->characteristics->contains($characteristic)) {
            return;
        }

        $this->characteristics->removeElement($characteristic);
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
