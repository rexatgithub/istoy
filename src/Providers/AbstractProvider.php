<?php

namespace Istoy\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Istoy\Contracts\OrderContract;

abstract class AbstractProvider
{
    /**
     * Create Provider instance
     *
     * @param Model|Collection<OrderContract> $model
     */
    public function __construct(protected OrderContract|Collection $model)
    {
    }

    /**
     * Add Order
     *
     * @param int|null $interval
     * @return void
     */
    abstract public function add(?int $interval = null): void;

    /**
     * Order Service base on Istoy\Providers\Factory;
     *
     * @see Istoy\Providers\Factory::PROVIDERS_*
     *
     * @return int
     */
    abstract public function getId(): int;

    /**
     * Check orders status
     *
     * @return void
     */
    abstract public function statuses(): void;
}

