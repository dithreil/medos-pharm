<?php

declare(strict_types=1);

namespace App\Utils;

use App\Exception\DateTimeUtilsException;
use DateTimeZone;

class DateTimeUtils
{
    public const DEFAULT_TIMEZONE = 'Europe/Moscow';
    public const FORMAT_DEFAULT = 'd.m.Y H:i:s';
    public const FORMAT_DATE = 'd.m.Y';
    public const FORMAT_FOR_PATH = 'Y-m-d';
    public const FORMAT_TIME = 'H:i';
    public const MONDAY = 1;
    public const SUNDAY = 7;
    public const HOUR = 3600;

    /**
     * @param string $value
     * @param string $format
     * @return \DateTimeImmutable
     * @throws DateTimeUtilsException
     */
    public static function parse(string $value, string $format = self::FORMAT_DEFAULT): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat($format, $value, new DateTimeZone(self::DEFAULT_TIMEZONE));
        if ($date === false) {
            throw new DateTimeUtilsException('Not correct date format');
        }
        return $date;
    }

    /**
     * @param \DateTimeInterface|null $date
     * @param string $format
     * @return string|null
     */
    public static function formatDate(?\DateTimeInterface $date, string $format = self::FORMAT_DEFAULT): ?string
    {
        if ($date !== null) {
            return $date->format($format);
        }

        return null;
    }

    /**
     * @return \DateTimeImmutable
     * @throws DateTimeUtilsException
     */
    public static function now(): \DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable('now', new DateTimeZone(self::DEFAULT_TIMEZONE));
        } catch (\Exception $exception) {
            throw new DateTimeUtilsException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param \DateTimeImmutable $origin
     * @param \DateTimeImmutable $target
     * @return int
     */
    public static function diff(\DateTimeImmutable $origin, \DateTimeImmutable $target): int
    {
        return $target->getTimestamp() - $origin->getTimestamp();
    }
}
