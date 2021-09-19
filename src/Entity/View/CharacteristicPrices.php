<?php

declare(strict_types=1);

namespace App\Entity\View;

use App\Repository\View\CharacteristicPricesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CharacteristicPricesRepository::class, readOnly=true)
 */
class CharacteristicPrices
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(name="id", type="string")
     */
    private string $id;

    /**
     * @var string
     * @ORM\Column(name="store", type="string")
     */
    private string $store;

    /**
     * @var string
     * @ORM\Column(name="store_name", type="string")
     */
    private string $storeName;

    /**
     * @var string
     * @ORM\Column(name="characteristic", type="string")
     */
    private string $characteristic;

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
}
