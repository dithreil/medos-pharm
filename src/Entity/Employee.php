<?php

declare(strict_types=1);

namespace App\Entity;

use App\DataProvider\EmployeeDataProvider;
use App\DataProvider\UserDataProvider;
use App\Model\Employee\WeeklyScheduleItem;
use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmployeeRepository::class)
 * @ORM\Table(name="employees")
 */
class Employee extends User
{
    /**
     * @var Store
     * @ORM\ManyToOne(targetEntity=Store::class, inversedBy="employees")
     */
    private Store $store;

    /**
     * @param Store $store
     * @param string $email
     * @param string $lastName
     * @param string $firstName
     * @param string|null $patronymic
     * @param string $phoneNumber
     * @param array $roles
     */
    public function __construct(
        Store $store,
        string $email,
        string $lastName,
        string $firstName,
        ?string $patronymic,
        string $phoneNumber,
        array $roles
    ) {
        parent::__construct($email, $lastName, $firstName, $patronymic, $phoneNumber, $roles);
        $this->store = $store;
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
     * @return array
     */
    public function getRoles(): array
    {
        $roles = [...parent::getRoles(), UserDataProvider::ROLE_EMPLOYEE];

        return array_unique($roles);
    }
}
