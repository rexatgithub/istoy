<?php

namespace Istoy\Tests\Unit\Providers;

use Istoy\Tests\TestCase;
use Istoy\Tests\Fixtures\Order;
use Istoy\Providers\Factory;
use Istoy\Providers\AbstractProvider;
use Istoy\Providers\Smm\Service;
use Mockery;

class FactoryTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_factory_has_smm_provider_constant()
    {
        $this->assertEquals(1, Factory::PROVIDER_SMM);
    }

    public function test_factory_creates_smm_provider_by_default()
    {
        $order = new Order();
        $provider = Factory::create($order);

        $this->assertInstanceOf(Service::class, $provider);
        $this->assertInstanceOf(AbstractProvider::class, $provider);
    }

    public function test_factory_creates_smm_provider_with_explicit_id()
    {
        $order = new Order();
        $provider = Factory::create($order, Factory::PROVIDER_SMM);

        $this->assertInstanceOf(Service::class, $provider);
    }

    public function test_factory_throws_exception_for_invalid_provider_id()
    {
        $order = new Order();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Provider with ID 999 not found');

        Factory::create($order, 999);
    }

    public function test_factory_registers_new_provider()
    {
        $customProviderClass = CustomTestProvider::class;
        
        Factory::register(2, $customProviderClass);
        
        $providers = Factory::getProviders();
        $this->assertArrayHasKey(2, $providers);
        $this->assertEquals($customProviderClass, $providers[2]);
    }

    public function test_factory_get_providers_returns_all_providers()
    {
        $providers = Factory::getProviders();
        
        $this->assertIsArray($providers);
        $this->assertArrayHasKey(Factory::PROVIDER_SMM, $providers);
        $this->assertEquals(Service::class, $providers[Factory::PROVIDER_SMM]);
    }

    public function test_factory_creates_registered_custom_provider()
    {
        $order = new Order();
        $customProviderClass = CustomTestProvider::class;
        
        Factory::register(2, $customProviderClass);
        
        $provider = Factory::create($order, 2);
        
        $this->assertInstanceOf(CustomTestProvider::class, $provider);
    }
}

// Test provider for testing custom provider registration
class CustomTestProvider extends AbstractProvider
{
    public function getId(): int
    {
        return 2;
    }

    public function add(?int $interval = null): void
    {
        // Test implementation
    }

    public function statuses(): void
    {
        // Test implementation
    }

    public function cancel(): void
    {
        // Test implementation
    }
}

