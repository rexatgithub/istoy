<?php

namespace Istoy\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Istoy\Contracts\OrderContract;
use Istoy\Providers\Smm;

class Factory
{
    /**
     * Put providers here
     */
    const PROVIDER_SMM = 1;

    /**
     * Available providers mapping
     * 
     * @var array<int, string>
     */
    protected static array $providers = [
        self::PROVIDER_SMM => Smm\Service::class,
    ];

    /**
     * Register a new provider
     *
     * @param int $id
     * @param string $providerClass
     * @return void
     */
    public static function register(int $id, string $providerClass): void
    {
        static::$providers[$id] = $providerClass;
    }

    /**
     * Get all registered providers
     *
     * @return array
     */
    public static function getProviders(): array
    {
        return static::$providers;
    }

    /**
     * Create Provider instance
     *
     * @param OrderContract|Collection<OrderContract> $model
     * @param int|null $providerId
     * @return AbstractProvider
     * @throws \Exception
     */
    public static function create(OrderContract|Collection $model, ?int $providerId = null): AbstractProvider
    {
        $providerId = $providerId ?? self::PROVIDER_SMM;
        $providerClass = static::$providers[$providerId] ?? null;

        if (!$providerClass) {
            throw new \Exception("Provider with ID {$providerId} not found");
        }

        if (!class_exists($providerClass)) {
            throw new \Exception("Provider class {$providerClass} does not exist");
        }

        return App::makeWith($providerClass, ['model' => $model]);
    }
}

