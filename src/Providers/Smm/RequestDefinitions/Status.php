<?php

namespace Istoy\Providers\Smm\RequestDefinitions;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

/**
 * @see https://smmlite.com/api Order Status Section
 */
class Status extends AbstractRequestDefinition
{
    public function method(): string
    {
        return self::HTTP_POST;
    }

    public function payload(): ?array
    {
        return [
            'key' => config('istoy.providers.smm.key'),
            'action' => self::ACTION_STATUS,
            ...$this->orderPayload(),
        ];
    }

    /**
     * Get Order Payload
     *
     * @return array
     */
    public function orderPayload(): array
    {
        //delimit by comma(,) if order provided is a collection of orders
        if ($this->model instanceof Collection) {
            return [
                'orders' => $this->model
                    ->pluck('external_id')
                    ->filter()
                    ->join(','),
            ];
        }

        return ['order' => $this->model->external_id];
    }

    public function rules(): array
    {
        return [
            'key' => ['required'],
            'action' => ['required', Rule::in(self::ACTIONS)],
            'order' => ['required_without:orders'],
            'orders' => ['required_without:order'],
        ];
    }

    /**
     * Request URL
     *
     * @return string
     */
    public function url(): string
    {
        return self::baseUrl();
    }
}

