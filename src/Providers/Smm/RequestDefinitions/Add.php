<?php

namespace Istoy\Providers\Smm\RequestDefinitions;

use Illuminate\Validation\Rule;
use Istoy\Providers\Smm\Enums\Action;

class Add extends AbstractRequestDefinition
{
    /**
     * Interval in minutes
     *
     * @var integer
     */
    protected $interval = 0;

    public function method(): string
    {
        return self::HTTP_POST;
    }

    public function action(): Action
    {
        return Action::Add;
    }

    public function orderPayload(): array
    {
        $payload = [
            'service' => $this->model->service,
            'link' => $this->model->link,
            'quantity' => $this->model->quantity,
        ];

        if ($this->interval) {
            $payload = [...$payload, 'interval' => $this->interval];
        }

        return $payload;
    }

    public function rules(): array
    {
        $rules = [
            ...parent::rules(),
            'service' => ['required', 'integer'],
            'link' => ['required', 'url'],
            'quantity' => ['required', 'integer'],
        ];

        if ($this->interval) {
            $rules = [...$rules, 'interval' => ['sometimes', 'integer']];
        }

        return $rules;
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

    /**
     * Set minutes interval
     *
     * @param integer $interval
     * @return void
     */
    public function setInterval(int $interval)
    {
        $this->interval = $interval;
    }
}

