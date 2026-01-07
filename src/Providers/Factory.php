<?php

namespace Istoy\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

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
        self::PROVIDER_SMM => \Istoy\Providers\Smm\Service::class,
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
     * @param Model|Collection $model
     * @param int|null $providerId
     * @return AbstractProvider
     * @throws \Exception
     */
    public static function create(Model|Collection $model, ?int $providerId = null): AbstractProvider
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

