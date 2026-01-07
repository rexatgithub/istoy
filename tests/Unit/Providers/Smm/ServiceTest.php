<?php

namespace Istoy\Tests\Unit\Providers\Smm;

use Istoy\Tests\TestCase;
use Istoy\Tests\Fixtures\Order;
use Istoy\Providers\Smm\Service;
use Istoy\Providers\Factory;
use Istoy\Models\Enums\OrderStatuses;
use Istoy\Providers\Smm\Enums\Statuses;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

class ServiceTest extends TestCase
{
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../../../database/migrations');
    }

    public function test_service_returns_correct_provider_id()
    {
        $order = new Order();
        $service = new Service($order);

        $this->assertEquals(Factory::PROVIDER_SMM, $service->getId());
    }

    public function test_add_creates_order_without_interval()
    {
        $order = new Order([
            'link' => 'https://example.com/post',
            'quantity' => 100,
            'service' => 1625,
        ]);
        $order->save();

        Http::fake([
            '*' => Http::response(['order' => '12345'], 200),
        ]);

        $service = new Service($order);
        $service->add();

        $order->refresh();
        $this->assertEquals('12345', $order->external_id);
        $this->assertEquals(Factory::PROVIDER_SMM, $order->service);
    }

    public function test_add_creates_order_with_interval()
    {
        $order = new Order([
            'link' => 'https://example.com/post',
            'quantity' => 100,
            'service' => 1625,
        ]);
        $order->save();

        Http::fake([
            '*' => Http::response(['order' => '12345'], 200),
        ]);

        $service = new Service($order);
        $service->add(interval: 5);

        $order->refresh();
        $this->assertEquals('12345', $order->external_id);
    }

    public function test_add_throws_exception_when_order_not_returned()
    {
        $order = new Order([
            'link' => 'https://example.com/post',
            'quantity' => 100,
            'service' => 1625,
        ]);
        $order->save();

        Http::fake([
            '*' => Http::response(['error' => 'Invalid request'], 200),
        ]);

        $service = new Service($order);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error pushing to external: model not found/created');

        $service->add();
    }

    public function test_statuses_updates_single_order()
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

        $service = new Service($order);
        $service->statuses();

        $order->refresh();
        $this->assertEquals(OrderStatuses::Completed, $order->status);
        $this->assertEquals(100, $order->start_count);
        $this->assertEquals(0, $order->remains);
    }

    public function test_statuses_updates_multiple_orders_from_collection()
    {
        $order1 = new Order([
            'external_id' => '12345',
            'status' => OrderStatuses::InProgress,
            'link' => 'https://example.com/post1',
            'quantity' => 100,
        ]);
        $order1->save();

        $order2 = new Order([
            'external_id' => '67890',
            'status' => OrderStatuses::InProgress,
            'link' => 'https://example.com/post2',
            'quantity' => 200,
        ]);
        $order2->save();

        Http::fake([
            '*' => Http::response([
                '12345' => [
                    'status' => 'Completed',
                    'start_count' => 100,
                    'remains' => 0,
                ],
                '67890' => [
                    'status' => 'In progress',
                    'start_count' => 200,
                    'remains' => 50,
                ],
            ], 200),
        ]);

        $collection = new Collection([$order1, $order2]);
        $service = new Service($collection);
        $service->statuses();

        $order1->refresh();
        $order2->refresh();

        $this->assertEquals(OrderStatuses::Completed, $order1->status);
        $this->assertEquals(OrderStatuses::InProgress, $order2->status);
        $this->assertEquals(50, $order2->remains);
    }

    public function test_statuses_skips_orders_without_status()
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
                    'start_count' => 100,
                    'remains' => 0,
                    // No status field
                ],
            ], 200),
        ]);

        $service = new Service($order);
        $service->statuses();

        // Order should remain unchanged
        $order->refresh();
        $this->assertEquals(OrderStatuses::InProgress, $order->status);
    }

    public function test_statuses_handles_cancelled_status()
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
                    'status' => 'Cancelled',
                    'start_count' => 50,
                    'remains' => 50,
                ],
            ], 200),
        ]);

        $service = new Service($order);
        $service->statuses();

        $order->refresh();
        $this->assertEquals(OrderStatuses::Cancelled, $order->status);
    }
}

