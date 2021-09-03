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
 * @package App\Entity
 * @ORM\Entity(repositoryClass=EmployeeRepository::class)
 * @ORM\Table(name="employees")
 */
class Employee extends User
{
    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="employee")
     */
    private Collection $orders;

    /**
     * @var int
     * @ORM\Column(name="code", type="smallint", nullable=true)
     */
    private ?int $code;

    /**
     * @var array
     */
    private array $weeklySchedule;

    /**
     * @param int|null $code
     * @param string $email
     * @param string $lastName
     * @param string $firstName
     * @param string|null $patronymic
     * @param string $phoneNumber
     * @param array $roles
     */
    public function __construct(
        ?int $code,
        string $email,
        string $lastName,
        string $firstName,
        ?string $patronymic,
        string $phoneNumber,
        array $roles
    ) {
        parent::__construct($email, $lastName, $firstName, $patronymic, $phoneNumber, $roles);

        $this->code = $code;

        $this->orders = new ArrayCollection();
        $this->weeklySchedule = EmployeeDataProvider::emptyWeeklySchedule();
    }

    /**
     * @return int|null
     */
    public function getCode(): ?int
    {
        return $this->code;
    }

    /**
     * @param int|null $code
     */
    public function setCode(?int $code): void
    {
        $this->code = $code;
    }

    public function isEqualForApi(int $specialityCode, string $lastName, string $firstName, ?string $patronymic): bool
    {
        return ($this->speciality->getCode() === $specialityCode &&
            $this->lastName === $lastName &&
            $this->firstName === $firstName &&
            $this->patronymic === $patronymic
        );
    }

    /**
     * @return WeeklyScheduleItem[]
     */
    public function getWeeklySchedule(): array
    {
        return $this->weeklySchedule;
    }

    /**
     * @param WeeklyScheduleItem[] $weeklySchedule
     */
    public function setWeeklySchedule(array $weeklySchedule): void
    {
        $this->weeklySchedule = $weeklySchedule;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = [...parent::getRoles(), UserDataProvider::ROLE_EMPLOYEE];

        return array_unique($roles);
    }

    /**
     * @return Area
     */
    public function getArea(): Area
    {
        return $this->area;
    }

    /**
     * @param Area $area
     */
    public function setArea(Area $area): void
    {
        if ($this->area === $area) {
            return;
        }

        $this->area = $area;
        $area->addEmployee($this);
    }

    /**
     * @return Speciality
     */
    public function getSpeciality(): Speciality
    {
        return $this->speciality;
    }

    /**
     * @param Speciality $speciality
     */
    public function setSpeciality(Speciality $speciality): void
    {
        if ($this->speciality === $speciality) {
            return;
        }

        $this->speciality = $speciality;
        $speciality->addEmployee($this);
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
        $order->setEmployee($this);
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

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    /**
     * @param Report $report
     */
    public function addReport(Report $report): void
    {
        if ($this->reports->contains($report)) {
            return;
        }

        $this->reports->add($report);
        $report->setEmployee($this);
    }

    /**
     * @param Report $report
     */
    public function removeReport(Report $report): void
    {
        if (!$this->reports->contains($report)) {
            return;
        }

        $this->reports->removeElement($report);
    }
}
