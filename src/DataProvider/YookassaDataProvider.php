<?php

declare(strict_types=1);

namespace App\DataProvider;

class YookassaDataProvider
{
    public const STATUS_WAITING_FOR_CAPTURE = 'waiting_for_capture';
    public const STATUS_SUCCEEDED = 'succeeded';

    /**
     * @param string $status
     * @return bool
     */
    public static function isStatusAllowed(string $status): bool
    {
        return $status === self::STATUS_WAITING_FOR_CAPTURE || $status === self::STATUS_SUCCEEDED;
    }
}
