<?php

declare(strict_types=1);

namespace App\Model\Employee;

class DailyScheduleItem
{
    /**
     * order begin time like 07:00
     * @var string
     */
    public string $beginTime;

    /**
     * order end time like 07:00
     * @var string
     */
    public string $endTime;

    /**
     * duration of order in minutes
     * @var int
     */
    public int $duration;

    /**
     * @var int
     */
    public int $employeeCode;

    /**
     * availability marker
     * @var int
     */
    public int $availability;

    /**
     * DailyScheduleItem constructor.
     * @param string $beginTime
     * @param string $endTime
     * @param int $duration
     * @param int $employeeCode
     * @param int $availability
     */
    public function __construct(string $beginTime, string $endTime, int $duration, int $employeeCode, int $availability)
    {
        $this->beginTime = $beginTime;
        $this->endTime = $endTime;
        $this->duration = $duration;
        $this->employeeCode = $employeeCode;
        $this->availability = $availability;
    }

    /**
     * @return string
     */
    public function getBeginTime(): string
    {
        return $this->beginTime;
    }

    /**
     * @param string $beginTime
     */
    public function setBeginTime(string $beginTime): void
    {
        $this->beginTime = $beginTime;
    }

    /**
     * @return string
     */
    public function getEndTime(): string
    {
        return $this->endTime;
    }

    /**
     * @param string $endTime
     */
    public function setEndTime(string $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return int
     */
    public function getAvailability(): int
    {
        return $this->availability;
    }

    /**
     * @param int $availability
     */
    public function setAvailability(int $availability): void
    {
        $this->availability = $availability;
    }

    /**
     * @return int
     */
    public function getEmployeeCode(): int
    {
        return $this->employeeCode;
    }

    /**
     * @param int $employeeCode
     */
    public function setEmployeeCode(int $employeeCode): void
    {
        $this->employeeCode = $employeeCode;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }
}
