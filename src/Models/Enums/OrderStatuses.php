<?php

namespace Istoy\Models\Enums;

use Istoy\Traits\EnumToArray;

enum OrderStatuses: string
{
    use EnumToArray;

    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case Paused = 'paused';
}

