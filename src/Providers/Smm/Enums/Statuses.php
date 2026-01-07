<?php

namespace Istoy\Providers\Smm\Enums;

use Istoy\Models\Enums\OrderStatuses;
use Istoy\Traits\EnumToArray;

/**
 * Order Status Enums
 *
 * For more about Enums
 * @see https://www.php.net/manual/en/language.types.enumerations.php
 *
 */
enum Statuses: string
{
    use EnumToArray;

    case Partial = 'Partial';
    case InProgress = 'In progress';
    case Completed = 'Completed';
    case Pending = 'Pending';
    case Processing = 'Processing';
    case Cancelled = 'Cancelled';

    /**
     * Get Equivalent order statuses
     *
     * @return OrderStatuses
     */
    public function orderStatus(): OrderStatuses
    {
        return match ($this) {
            self::Partial => OrderStatuses::InProgress,
            self::InProgress => OrderStatuses::InProgress,
            self::Completed => OrderStatuses::Completed,
            self::Pending => OrderStatuses::InProgress,
            self::Processing => OrderStatuses::InProgress,
            self::Cancelled => OrderStatuses::Cancelled,
            default => OrderStatuses::Pending,
        };
    }
}

