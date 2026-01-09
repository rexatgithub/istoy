<?php

namespace Istoy\Providers\Smm\Enums;

use Istoy\Traits\EnumToArray;


enum Action: string
{
    use EnumToArray;

    case Add = 'add';
    case Status = 'status';
    case Cancel = 'cancel';
}

