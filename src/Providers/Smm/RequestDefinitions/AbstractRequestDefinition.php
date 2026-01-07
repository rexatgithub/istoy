<?php

namespace Istoy\Providers\Smm\RequestDefinitions;

use Istoy\RequestDefinitions\RequestDefinition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class AbstractRequestDefinition extends RequestDefinition
{
    /**
     * Action type here
     */
    public const ACTION_ADD = 'add';
    public const ACTION_STATUS = 'status';

    public const ACTIONS = [self::ACTION_ADD, self::ACTION_STATUS];

    /**
     * SMM Base URL
     *
     * @return string
     */
    public static function baseUrl(): string
    {
        return config('istoy.providers.smm.host');
    }

    public function __construct(protected Model|Collection $model)
    {
        //
    }
}

