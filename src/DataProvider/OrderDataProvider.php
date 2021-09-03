<?php

declare(strict_types=1);

namespace App\DataProvider;

class OrderDataProvider
{
    public const DEFAULT_ORDER_LIMIT = 10;

    public const COMMUNICATION_NONE = null;
    public const COMMUNICATION_SKYPE = 'skype';
    public const COMMUNICATION_WHATS_APP = 'whats app';

    public const STATUS_NEW = 'new';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELLED = 'cancelled';

    public const TYPE_ONLINE = 'I';
    public const TYPE_VISIT = 'V';

    public const ACTION_CANCEL = 'action.cancel';
    public const ACTION_RATE = 'action.rate';

    public const RATING_DISGUSTING = 1;
    public const RATING_BAD = 2;
    public const RATING_SATISFACTORY = 3;
    public const RATING_GOOD = 4;
    public const RATING_PERFECT = 5;

    /**
     * @param string|null $communication
     * @return bool
     */
    public static function isCommunicationAllowed(?string $communication): bool
    {
        $allowedCommunications = [
            self::COMMUNICATION_NONE,
            self::COMMUNICATION_SKYPE,
            self::COMMUNICATION_WHATS_APP
        ];
        return in_array($communication, $allowedCommunications);
    }

    /**
     * @param int $rating
     * @return bool
     */
    public static function isRatingAllowed(int $rating): bool
    {
        $allowedRate = [
            self::RATING_DISGUSTING,
            self::RATING_BAD,
            self::RATING_SATISFACTORY,
            self::RATING_GOOD,
            self::RATING_PERFECT
        ];
        return in_array($rating, $allowedRate);
    }

    /**
     * @param int $rating
     * @param string|null $ratingComment
     * @return bool
     */
    public static function isRatingCommentRequired(int $rating, ?string $ratingComment): bool
    {
        return $rating <= OrderDataProvider::RATING_SATISFACTORY && $ratingComment === null;
    }

    /**
     * @param string $status
     * @return bool
     */
    public static function isStatusAllowed(string $status): bool
    {
        $allowedStatuses = [
            self::STATUS_NEW,
            self::STATUS_DONE,
            self::STATUS_CANCELLED
        ];
        return in_array($status, $allowedStatuses);
    }

    /**
     * @param string $type
     * @return bool
     */
    public static function isTypeAllowed(string $type): bool
    {
        $allowedStatuses = [
            self::TYPE_ONLINE,
            self::TYPE_VISIT
        ];
        return in_array($type, $allowedStatuses);
    }
}
