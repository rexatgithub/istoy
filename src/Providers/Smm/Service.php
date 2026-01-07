<?php

namespace Istoy\Providers\Smm;

use Istoy\Contracts\OrderContract;
use Istoy\Providers\AbstractProvider;
use Istoy\Providers\Factory;
use Istoy\Providers\Smm\Enums\Statuses;
use Istoy\Providers\Smm\RequestDefinitions\Add;
use Istoy\Providers\Smm\RequestDefinitions\Status;
use Exception;

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

            // Get the order model class from config or use a default
            $orderClass = config('istoy.order_model');
            
            if (!$orderClass || !class_exists($orderClass)) {
                throw new Exception('Order model class not configured or does not exist');
            }

            $orderClass::withExternalId($externalId)
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
}

