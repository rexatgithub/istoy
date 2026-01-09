<?php

namespace Istoy\Providers\Smm;

use Istoy\Providers\AbstractProvider;
use Istoy\Providers\Factory;
use Istoy\Providers\Smm\Enums\Statuses;
use Istoy\Providers\Smm\RequestDefinitions\Add;
use Istoy\Providers\Smm\RequestDefinitions\Status;
use Exception;
use Istoy\Models\Enums\OrderStatuses;
use Istoy\Providers\Smm\RequestDefinitions\Cancel;
use Istoy\Services\OrderService;

class Service extends AbstractProvider
{
    /**
     * Get the provider ID
     *
     * @return int
     */
    public function getId(): int
    {
        return Factory::PROVIDER_SMM;
    }

    /**
     * Add Order
     * 
     * Push the order to the external provider
     *
     * @param int|null $interval
     * @return void
     * @throws Exception
     */
    public function add(?int $interval = null): void
    {
        $addService = new Add($this->model);

        if ($interval) {
            $addService->setInterval($interval);
        }

        $order = $addService
            ->send()
            ->throw()
            ->json('order');

        if (!$order) {
            throw new Exception(
                'Error pushing to external: model not found/created',
            );
        }

        $this->model->update([
            'external_id' => $order,
            'service' => $this->getId(),
        ]);
    }

    /**
     * Check orders status
     * 
     * Update the order status based on the external provider response
     *
     * @return void
     */
    public function statuses(): void
    {
        $response = (new Status($this->model))->send()->throw();

        collect($response->json())->each(function (
            $externalRecord,
            $externalId,
        ) {
            if (!isset($externalRecord['status'])) {
                return;
            }

            OrderService::orderFqn()::withExternalId($externalId)
                ->first()
                ?->update([
                    'status' => Statuses::tryFrom(
                        $externalRecord['status'] ?? null,
                    )?->orderStatus(),
                    'start_count' => $externalRecord['start_count'] ?? 0,
                    'remains' => ($externalRecord['remains'] ?? 0) * 1,
                ]);
        });
    }

    /**
     * Cancel Order
     *
     * @return void
     */
    public function cancel(): void
    {
        $response = (new Cancel($this->model))->send()->throw();

        /**
         * Only update the successfully cancelled orders
         * 
         * @todo record the failed cancel messages
         */
        $orderIds = collect($response->json())
            ->filter(fn($order) => $order['cancel'] === 1)
            ->values()
            ->pluck('order');

        if ($orderIds->isEmpty()) {
            return;
        }

        OrderService::orderFqn()::whereIn('external_id', $orderIds)->update([
            'status' => OrderStatuses::Cancelled,
        ]);
    }
}

