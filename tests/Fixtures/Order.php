<?php

namespace Istoy\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Istoy\Contracts\OrderContract;
use Istoy\Models\Enums\OrderStatuses;
use Istoy\Traits\HasIstoyFields;

class Order extends Model implements OrderContract
{
    use HasIstoyFields;

    protected $table = 'orders';

    protected $casts = [
        'status' => OrderStatuses::class,
    ];

    /**
     * Scope to find order by external ID
     *
     * @param Builder $query
     * @param string|int $externalId
     * @return Builder
     */
    public function scopeWithExternalId(Builder $query, string|int $externalId): Builder
    {
        return $query->where('external_id', $externalId);
    }
}

