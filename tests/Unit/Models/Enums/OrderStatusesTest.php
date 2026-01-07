<?php

namespace Istoy\Tests\Unit\Models\Enums;

use Istoy\Tests\TestCase;
use Istoy\Models\Enums\OrderStatuses;

class OrderStatusesTest extends TestCase
{
    public function test_order_statuses_enum_has_all_expected_cases()
    {
        $cases = OrderStatuses::cases();
        $expectedCases = [
            'Pending',
            'InProgress',
            'Cancelled',
            'Completed',
            'Paused',
        ];

        $this->assertCount(count($expectedCases), $cases);

        foreach ($expectedCases as $expectedCase) {
            $this->assertTrue(
                in_array($expectedCase, array_column($cases, 'name')),
                "Expected case {$expectedCase} not found"
            );
        }
    }

    public function test_order_statuses_values_are_correct()
    {
        $this->assertEquals('pending', OrderStatuses::Pending->value);
        $this->assertEquals('in_progress', OrderStatuses::InProgress->value);
        $this->assertEquals('cancelled', OrderStatuses::Cancelled->value);
        $this->assertEquals('completed', OrderStatuses::Completed->value);
        $this->assertEquals('paused', OrderStatuses::Paused->value);
    }

    public function test_enum_to_array_trait_works()
    {
        $names = OrderStatuses::names();
        $values = OrderStatuses::values();
        $array = OrderStatuses::array();

        $this->assertIsArray($names);
        $this->assertIsArray($values);
        $this->assertIsArray($array);
        $this->assertCount(count($names), $values);
        $this->assertCount(count($names), $array);
    }
}

