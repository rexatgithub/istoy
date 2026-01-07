<?php

namespace Istoy\Services;

use Istoy\Models\Enums\OrderStatuses;
use Istoy\Providers\Factory;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(protected Model|Collection $instance)
    {
    }

    /**
     * Start the order process
     *
     * @param int|null $interval
     * @return bool
     */
    public function start(?int $interval = null): bool
    {
        try {
            DB::beginTransaction();

            $this->persist()->push($interval);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $this->persist([
                'status' => OrderStatuses::Cancelled,
            ]);

            report($e);
            return false;
        }
        return true;
    }

    /**
     * Persist DB changes for the instance/model
     *
     * @param array $attr
     * @return static
     */
    private function persist(array $attr = []): static
    {
        $this->instance->update([...$this->inProgressAttributes(), ...$attr]);

        return $this;
    }

    /**
     * In progress attributes
     *
     * This means - to auto start the order
     *
     * @return array
     */
    private function inProgressAttributes(): array
    {
        return [
            'status' => OrderStatuses::InProgress,
        ];
    }

    /**
     * Push order to provider
     *
     * @param int|null $interval
     * @return static
     */
    private function push(?int $interval = null): static
    {
        if ($this->skipPush()) {
            return $this;
        }

        Factory::create($this->instance)->add($interval);

        return $this;
    }

    /**
     * Determine if push should be skipped
     *
     * @return bool
     */
    public function skipPush(): bool
    {
        return false;
    }

    /**
     * Sync local and external service order statuses
     *
     * @return self
     */
    public function syncStatuses(): self
    {
        Factory::create($this->instance)->statuses();

        return $this;
    }
}

