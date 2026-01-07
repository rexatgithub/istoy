<?php

namespace Istoy\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Contract for Order model that the package expects
 */
interface OrderContract
{
    /**
     * Update the order with given attributes
     *
     * @param array $attributes
     * @param array $options
     * @return bool|static
     */
    public function update(array $attributes = [], array $options = []);

    /**
     * Scope to find order by external ID
     *
     * @param Builder $query
     * @param string|int $externalId
     * @return Builder
     */
    public function scopeWithExternalId(Builder $query, string|int $externalId): Builder;
}

