<?php

declare(strict_types=1);

namespace App\Entity;

use App\DataProvider\UserDataProvider;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @package App\Entity
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @ORM\Table(name="clients")
 */
class Client extends User
{
    /**
     * @var string|null
     * @ORM\Column(name="snils", type="string", length=11, nullable=true)
     */
    private ?string $snils;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="birth_date", type="datetime_immutable")
     */
    private \DateTimeImmutable $birthDate;

    /**
     * @var string|null
     * @ORM\Column(name="skype", type="string", length=255, nullable=true)
     */
    private ?string $skype;

    /**
     * @var string|null
     * @ORM\Column(name="whatsapp", type="string", length=11, nullable=true)
     */
    private ?string $whatsapp;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="client")
     */
    private Collection $orders;

    /**
     * Client constructor.
     * @param string $email
     * @param string $lastName
     * @param string $firstName
     * @param string|null $patronymic
     * @param \DateTimeImmutable $birthDate
     * @param string $phoneNumber
     * @param array $roles
     */
    public function __construct(
        string $email,
        string $lastName,
        string $firstName,
        ?string $patronymic,
        \DateTimeImmutable $birthDate,
        string $phoneNumber,
        array $roles
    ) {
        parent::__construct($email, $lastName, $firstName, $patronymic, $phoneNumber, $roles);

        $this->birthDate = $birthDate;
        $this->skype = null;
        $this->whatsapp = null;
        $this->snils = null;
        $this->orders = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = [...parent::getRoles(), UserDataProvider::ROLE_CLIENT];

        return array_unique($roles);
    }

    /**
     * @return string|null
     */
    public function getSnils(): ?string
    {
        return $this->snils;
    }

    /**
     * @param string|null $snils
     */
    public function setSnils(?string $snils): void
    {
        $this->snils = $snils;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTimeImmutable $birthDate
     */
    public function setBirthDate(\DateTimeImmutable $birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return string|null
     */
    public function getSkype(): ?string
    {
        return $this->skype;
    }

    /**
     * @param string|null $skype
     */
    public function setSkype(?string $skype): void
    {
        $this->skype = $skype;
    }

    /**
     * @return string|null
     */
    public function getWhatsapp(): ?string
    {
        return $this->whatsapp;
    }

    /**
     * @param string|null $whatsapp
     */
    public function setWhatsapp(?string $whatsapp): void
    {
        $this->whatsapp = $whatsapp;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @param Order $order
     */
    public function addOrder(Order $order): void
    {
        if ($this->orders->contains($order)) {
            return;
        }

        $this->orders->add($order);
        $order->setClient($this);
    }

    /**
     * @param Order $order
     */
    public function removeOrder(Order $order): void
    {
        if (!$this->orders->contains($order)) {
            return;
        }

        $this->orders->removeElement($order);
    }
}
