<?php

declare(strict_types=1);

namespace App\DataProvider;

class DocumentDataProvider
{
    public const TYPE_INCOME = 'income';
    public const TYPE_OUTCOME = 'outcome';
    public const TYPE_TRANSFER = 'transfer';

    /**
     * @return array|string[]
     */
    public static function types(): array
    {
        return [
            self::TYPE_INCOME,
            self::TYPE_OUTCOME,
            self::TYPE_TRANSFER
        ];
    }

    /**
     * @param string $type
     * @return bool
     */
    public static function isTypeAllowed(string $type): bool
    {
        return in_array($type, self::types());
    }
}
