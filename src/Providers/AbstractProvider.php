<?php

namespace Istoy\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class AbstractProvider
{
    /**
     * Add Order
     *
     * @param int|null $interval
     * @return void
     */
    abstract public function add(?int $interval = null);

    /**
     * Order Service base on Istoy\Providers\Factory;
     *
     * @see Istoy\Providers\Factory::PROVIDERS_*
     *
     * @return int
     */
    abstract public function getId();

    /**
     * Check orders status
     *
     * @return void
     */
    abstract public function statuses();

    /**
     * Create Provider instance
     *
     * @param Model|Collection $model
     */
    public function __construct(protected Model|Collection $model)
    {
    }
}

