<?php

namespace Istoy\Tests\Unit\Providers\Smm\RequestDefinitions;

use Istoy\Tests\TestCase;
use Istoy\Tests\Fixtures\Order;
use Istoy\Providers\Smm\RequestDefinitions\Add;
use Illuminate\Support\Facades\Config;

class AddTest extends TestCase
{
    public function test_add_request_definition_has_correct_method()
    {
        $order = new Order();
        $add = new Add($order);

        $this->assertEquals('POST', $add->method());
    }

    public function test_add_request_definition_has_correct_url()
    {
        Config::set('istoy.providers.smm.host', 'https://smmlite.com/api/v2');
        
        $order = new Order();
        $add = new Add($order);

        $this->assertEquals('https://smmlite.com/api/v2', $add->url());
    }

    public function test_add_payload_includes_required_fields()
    {
        Config::set('istoy.providers.smm.key', 'test_api_key');
        
        $order = new Order([
            'service' => 1625,
            'link' => 'https://example.com/post',
            'quantity' => 100,
        ]);

        $add = new Add($order);
        $payload = $add->payload();

        $this->assertIsArray($payload);
        $this->assertEquals('test_api_key', $payload['key']);
        $this->assertEquals('add', $payload['action']);
        $this->assertEquals(1625, $payload['service']);
        $this->assertEquals('https://example.com/post', $payload['link']);
        $this->assertEquals(100, $payload['quantity']);
    }

    public function test_add_payload_includes_interval_when_set()
    {
        Config::set('istoy.providers.smm.key', 'test_api_key');
        
        $order = new Order([
            'service' => 1625,
            'link' => 'https://example.com/post',
            'quantity' => 100,
        ]);

        $add = new Add($order);
        $add->setInterval(5);
        $payload = $add->payload();

        $this->assertArrayHasKey('interval', $payload);
        $this->assertEquals(5, $payload['interval']);
    }

    public function test_add_payload_excludes_interval_when_not_set()
    {
        Config::set('istoy.providers.smm.key', 'test_api_key');
        
        $order = new Order([
            'service' => 1625,
            'link' => 'https://example.com/post',
            'quantity' => 100,
        ]);

        $add = new Add($order);
        $payload = $add->payload();

        $this->assertArrayNotHasKey('interval', $payload);
    }

    public function test_add_validation_rules_are_correct()
    {
        $order = new Order();
        $add = new Add($order);
        $rules = $add->rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('key', $rules);
        $this->assertArrayHasKey('action', $rules);
        $this->assertArrayHasKey('service', $rules);
        $this->assertArrayHasKey('link', $rules);
        $this->assertArrayHasKey('quantity', $rules);
        $this->assertContains('required', $rules['key']);
        $this->assertContains('required', $rules['service']);
        $this->assertContains('required', $rules['link']);
        $this->assertContains('required', $rules['quantity']);
    }

    public function test_add_validation_rules_include_interval_when_set()
    {
        $order = new Order();
        $add = new Add($order);
        $add->setInterval(5);
        $rules = $add->rules();

        $this->assertArrayHasKey('interval', $rules);
    }
}

