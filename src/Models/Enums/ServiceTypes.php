<?php

namespace Istoy\Models\Enums;

use Istoy\Traits\EnumToArray;

enum ServiceTypes: string
{
    use EnumToArray;

    case Likes = 'likes';
    case Views = 'views';
    case Comments = 'comments';
    case Followers = 'followers';
}

