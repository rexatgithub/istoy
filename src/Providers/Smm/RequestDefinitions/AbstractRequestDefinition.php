<?php

namespace Istoy\Providers\Smm\RequestDefinitions;

use Istoy\RequestDefinitions\RequestDefinition;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Istoy\Contracts\OrderContract;
use Istoy\Providers\Smm\Enums\Action;

abstract class AbstractRequestDefinition extends RequestDefinition
{
    /**
     * SMM Base URL
     *
     * @return string
     */
    public static function baseUrl(): string
    {
        return config('istoy.providers.smm.host');
    }

    public function __construct(protected OrderContract|Collection $model)
    {
        //
    }

   
    abstract public function action(): Action;

    public function payload(): array
    {
        return [
            'key' => config('istoy.providers.smm.key'),
            'action' => $this->action()->value,
            ...$this->orderPayload(),
        ];
    }

    public function rules(): array
    {
        return [
            'key' => ['required'],
            'action' => ['required', Rule::enum(Action::class)],
        ];
    }

    abstract public function orderPayload(): array;
}

