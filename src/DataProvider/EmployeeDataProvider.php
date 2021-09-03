<?php

declare(strict_types=1);

namespace App\DataProvider;

use App\Exception\AppException;
use App\Model\Employee\WeeklyScheduleItem;
use App\Utils\DateTimeUtils;

class EmployeeDataProvider
{
    public const BASE_EMPLOYEE_CODE = 10000;
    public const DAY_STATUS_AVAILABLE = 0;
    public const DAY_STATUS_BUSY = 1;
    public const DAY_STATUS_NOT_AVAILABLE = 2;

    /**
     * @return WeeklyScheduleItem[]
     */
    public static function emptyWeeklySchedule(): array
    {
        return array(
            new WeeklyScheduleItem(),
            new WeeklyScheduleItem(),
            new WeeklyScheduleItem(),
            new WeeklyScheduleItem(),
            new WeeklyScheduleItem(),
            new WeeklyScheduleItem(),
            new WeeklyScheduleItem()
        );
    }

    /**
     * @param array $weekSchedule
     * @param string $date
     * @return WeeklyScheduleItem[]
     * @throws AppException
     */
    public static function transformPlainWeekScheduleToItems(array $weekSchedule, string $date): array
    {
        $weekScheduleItems = [];

        $lastSunday = DateTimeUtils::parse($date, DateTimeUtils::FORMAT_FOR_PATH)->modify('sunday last week');

        for ($day = DateTimeUtils::MONDAY; $day <= DateTimeUtils::SUNDAY; $day++) {
            $weekDay = DateTimeUtils::formatDate($lastSunday->modify("+ {$day} days"), DateTimeUtils::FORMAT_FOR_PATH);
            $weekScheduleItems[] = new WeeklyScheduleItem($weekDay, $weekSchedule[$day - 1]['h'], $weekSchedule[$day - 1]['b']);
        }

        return $weekScheduleItems;
    }
}
