<?php

namespace Istoy\Tests\Unit\Providers\Smm\RequestDefinitions;

use Istoy\Tests\TestCase;
use Istoy\Tests\Fixtures\Order;
use Istoy\Providers\Smm\RequestDefinitions\Status;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;

class StatusTest extends TestCase
{
    public function test_status_request_definition_has_correct_method()
    {
        $order = new Order();
        $status = new Status($order);

        $this->assertEquals('POST', $status->method());
    }

    public function test_status_request_definition_has_correct_url()
    {
        Config::set('istoy.providers.smm.host', 'https://smmlite.com/api/v2');
        
        $order = new Order();
        $status = new Status($order);

        $this->assertEquals('https://smmlite.com/api/v2', $status->url());
    }

    public function test_status_payload_for_single_order()
    {
        Config::set('istoy.providers.smm.key', 'test_api_key');
        
        $order = new Order([
            'external_id' => '12345',
        ]);

        $status = new Status($order);
        $payload = $status->payload();

        $this->assertIsArray($payload);
        $this->assertEquals('test_api_key', $payload['key']);
        $this->assertEquals('status', $payload['action']);
        $this->assertEquals('12345', $payload['order']);
        $this->assertArrayNotHasKey('orders', $payload);
    }

    public function test_status_payload_for_collection()
    {
        Config::set('istoy.providers.smm.key', 'test_api_key');
        
        $order1 = new Order(['external_id' => '12345']);
        $order2 = new Order(['external_id' => '67890']);
        $order3 = new Order(['external_id' => null]); // Should be filtered out
        
        $collection = new Collection([$order1, $order2, $order3]);

        $status = new Status($collection);
        $payload = $status->payload();

        $this->assertIsArray($payload);
        $this->assertEquals('test_api_key', $payload['key']);
        $this->assertEquals('status', $payload['action']);
        $this->assertEquals('12345,67890', $payload['orders']);
        $this->assertArrayNotHasKey('order', $payload);
    }

    public function test_status_payload_filters_null_external_ids()
    {
        Config::set('istoy.providers.smm.key', 'test_api_key');
        
        $order1 = new Order(['external_id' => '12345']);
        $order2 = new Order(['external_id' => null]);
        $order3 = new Order(['external_id' => '67890']);
        
        $collection = new Collection([$order1, $order2, $order3]);

        $status = new Status($collection);
        $payload = $status->payload();

        $this->assertEquals('12345,67890', $payload['orders']);
    }

    public function test_status_validation_rules_are_correct()
    {
        $order = new Order();
        $status = new Status($order);
        $rules = $status->rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('key', $rules);
        $this->assertArrayHasKey('action', $rules);
        $this->assertArrayHasKey('order', $rules);
        $this->assertArrayHasKey('orders', $rules);
        $this->assertContains('required', $rules['key']);
        $this->assertContains('required', $rules['action']);
        $this->assertContains('required_without:orders', $rules['order']);
        $this->assertContains('required_without:order', $rules['orders']);
    }
}

