<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\AppException;
use App\Repository\ProducerRepository;
use App\Utils\DateTimeUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=ProducerRepository::class)
 * @ORM\Table(name="producers")
 */
class Producer
{
    /**
     * @var string
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id()
     */
    private string $id;

    /**
     * @var string
     * @ORM\Column(name="short_name", type="string", length=20)
     */
    private string $shortName;

    /**
     * @var string
     * @ORM\Column(name="full_name", type="string")
     */
    private string $fullName;

    /**
     * @var string
     * @ORM\Column(name="country", type="string", length=100)
     */
    private string $country;

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
     * @var Collection|Nomenclature[]
     * @ORM\OneToMany(targetEntity=Nomenclature::class, mappedBy="producer")
     */
    private Collection $nomenclatures;

    /**
     * @param string $shortName
     * @param string $fullName
     * @param string $country
     * @throws AppException
     */
    public function __construct(string $shortName, string $fullName, string $country)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->shortName = $shortName;
        $this->fullName = $fullName;
        $this->country = $country;

        $this->nomenclatures = new ArrayCollection();

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
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @param string $shortName
     */
    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return Collection|Nomenclature[]
     */
    public function getNomenclatures(): Collection
    {
        return $this->nomenclatures;
    }

    /**
     * @param Nomenclature $nomenclature
     */
    public function addNomenclature(Nomenclature $nomenclature): void
    {
        if ($this->nomenclatures->contains($nomenclature)) {
            return;
        }

        $this->nomenclatures->add($nomenclature);
        $nomenclature->setProducer($this);
    }

    /**
     * @param Nomenclature $nomenclature
     */
    public function removeNomenclature(Nomenclature $nomenclature): void
    {
        if (!$this->nomenclatures->contains($nomenclature)) {
            return;
        }

        $this->nomenclatures->removeElement($nomenclature);
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
