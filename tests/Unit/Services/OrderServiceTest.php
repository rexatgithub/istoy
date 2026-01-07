<?php

namespace Istoy\Tests\Unit\Services;

use Istoy\Tests\TestCase;
use Istoy\Tests\Fixtures\Order;
use Istoy\Services\OrderService;
use Istoy\Models\Enums\OrderStatuses;
use Istoy\Providers\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Mockery;

class OrderServiceTest extends TestCase
{
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_order_service_can_be_instantiated_with_model()
    {
        $order = new Order([
            'link' => 'https://example.com/post',
            'quantity' => 100,
        ]);
        $service = new OrderService($order);

        $this->assertInstanceOf(OrderService::class, $service);
    }

    public function test_order_service_can_be_instantiated_with_collection()
    {
        $orders = collect([new Order(), new Order()]);
        $service = new OrderService($orders);

        $this->assertInstanceOf(OrderService::class, $service);
    }

    public function test_skip_push_returns_false_by_default()
    {
        $order = new Order();
        $service = new OrderService($order);

        $this->assertFalse($service->skipPush());
    }

    public function test_sync_statuses_calls_provider_statuses()
    {
        $order = new Order([
            'external_id' => '12345',
            'status' => OrderStatuses::InProgress,
            'link' => 'https://example.com/post',
            'quantity' => 100,
        ]);
        $order->save();

        Http::fake([
            '*' => Http::response([
                '12345' => [
                    'status' => 'Completed',
                    'start_count' => 100,
                    'remains' => 0,
                ],
            ], 200),
        ]);

        $service = new OrderService($order);
        $result = $service->syncStatuses();

        $this->assertInstanceOf(OrderService::class, $result);
    }

    public function test_start_sets_order_to_in_progress_on_success()
    {
        $order = new Order([
            'link' => 'https://example.com/post',
            'quantity' => 100,
            'service' => 1625,
            'status' => OrderStatuses::Pending,
        ]);
        $order->save();

        Http::fake([
            '*' => Http::response(['order' => '12345'], 200),
        ]);

        $service = new OrderService($order);
        $result = $service->start();

        $this->assertTrue($result);
        $order->refresh();
        $this->assertEquals(OrderStatuses::InProgress, $order->status);
    }

    public function test_start_sets_order_to_cancelled_on_failure()
    {
        $order = new Order([
            'link' => 'https://example.com/post',
            'quantity' => 100,
            'service' => 1625,
            'status' => OrderStatuses::Pending,
        ]);
        $order->save();

        Http::fake([
            '*' => Http::response(['error' => 'Invalid request'], 400),
        ]);

        $service = new OrderService($order);
        $result = $service->start();

        $this->assertFalse($result);
        $order->refresh();
        $this->assertEquals(OrderStatuses::Cancelled, $order->status);
    }

    public function test_start_with_interval_passes_interval_to_provider()
    {
        $order = new Order([
            'link' => 'https://example.com/post',
            'quantity' => 100,
            'service' => 1625,
            'status' => OrderStatuses::Pending,
        ]);
        $order->save();

        Http::fake([
            '*' => Http::response(['order' => '12345'], 200),
        ]);

        $service = new OrderService($order);
        $result = $service->start(interval: 5);

        $this->assertTrue($result);
    }
}

