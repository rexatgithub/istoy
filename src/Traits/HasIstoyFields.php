<?php

namespace Istoy\Traits;

/**
 * Trait that automatically adds required Istoy fields to the model's fillable array.
 * 
 * This trait ensures that all required fields for Istoy package are automatically
 * included in the model's fillable array without manual configuration.
 * 
 * Simply use this trait in your Order model:
 * 
 * ```php
 * use Istoy\Traits\HasIstoyFields;
 * 
 * class Order extends Model
 * {
 *     use HasIstoyFields;
 *     // All Istoy fields are now automatically fillable!
 * }
 * ```
 */
trait HasIstoyFields
{
    /**
     * Initialize the trait and merge Istoy fields with existing fillable array.
     */
    public function initializeHasIstoyFields(): void
    {
        $istoyFields = [
            'external_id',
            'service',
            'link',
            'quantity',
            'status',
            'start_count',
            'remains',
        ];

        // Merge Istoy fields with existing fillable, avoiding duplicates
        $this->fillable = array_unique(array_merge($this->fillable ?? [], $istoyFields));
    }
}

