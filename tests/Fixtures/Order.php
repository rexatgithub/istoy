<?php

namespace Istoy\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Istoy\Contracts\OrderContract;
use Istoy\Models\Enums\OrderStatuses;

class Order extends Model implements OrderContract
{
    protected $table = 'orders';

    protected $fillable = [
        'external_id',
        'service',
        'link',
        'quantity',
        'status',
        'start_count',
        'remains',
    ];

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

