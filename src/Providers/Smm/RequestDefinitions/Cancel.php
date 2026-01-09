<?php

namespace Istoy\Providers\Smm\RequestDefinitions;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Istoy\Providers\Smm\Enums\Action;

/**
 * @link https://smmlite.com/api Order Status Section
 */
class Cancel extends AbstractRequestDefinition
{
    public function method(): string
    {
        return self::HTTP_POST;
    }

    public function action(): Action
    {
        return Action::Cancel;
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
            ...parent::rules(),
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

