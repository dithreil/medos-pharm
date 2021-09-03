<?php

declare(strict_types=1);

namespace App\Model\Employee;

class WeeklyScheduleItem
{
    /**
     * work time for day like 07:00-16:00
     * @var string|null
     */
    public ?string $workTime;

    /**
     * availability marker
     * @var int|null
     */
    public ?int $availability;

    /**
     * date
     * @var string|null
     */
    public ?string $date;

    /**
     * WeeklyScheduleItem constructor.
     * @param string|null $date
     * @param string|null $workTime
     * @param int|null $availability
     */
    public function __construct(?string $date = null, ?string $workTime = null, ?int $availability = null)
    {
        $this->date = $date;
        $this->workTime = $workTime;
        $this->availability = $availability;
    }
}
