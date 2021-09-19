<?php

declare(strict_types=1);

namespace App\Entity\View;

use App\Repository\View\WaresRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WaresRepository::class, readOnly=true)
 */
class Wares
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(name="characteristic", type="string")
     */
    private string $characteristic;

    /**
     * @var string
     * @ORM\Column(name="nomenclature", type="string")
     */
    private string $nomenclature;

    /**
     * @var string
     * @ORM\Column(name="store", type="string")
     */
    private string $store;

    /**
     * @var int
     * @ORM\Column(name="medical_form", type="integer")
     */
    private int $medicalForm;

    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private string $name;

    /**
     * @var string
     * @ORM\Column(name="serial", type="string")
     */
    private string $serial;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="expire_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $expireTime;

    /**
     * @var float
     * @ORM\Column(name="retail_price", type="float")
     */
    private float $retailPrice;

    /**
     * @var float
     * @ORM\Column(name="stock", type="float")
     */
    private float $stock;

    /**
     * @return string
     */
    public function getCharacteristic(): string
    {
        return $this->characteristic;
    }

    /**
     * @return string
     */
    public function getNomenclature(): string
    {
        return $this->nomenclature;
    }

    /**
     * @return string
     */
    public function getStore(): string
    {
        return $this->store;
    }

    /**
     * @return int
     */
    public function getMedicalForm(): int
    {
        return $this->medicalForm;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSerial(): string
    {
        return $this->serial;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpireTime(): \DateTimeImmutable
    {
        return $this->expireTime;
    }

    /**
     * @return float
     */
    public function getRetailPrice(): float
    {
        return $this->retailPrice;
    }

    /**
     * @return float
     */
    public function getStock(): float
    {
        return $this->stock;
    }
}
