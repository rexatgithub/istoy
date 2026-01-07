<?php

namespace Istoy\Tests\Unit\Providers\Smm\Enums;

use Istoy\Tests\TestCase;
use Istoy\Providers\Smm\Enums\Statuses;
use Istoy\Models\Enums\OrderStatuses;

class StatusesTest extends TestCase
{
    public function test_statuses_enum_has_all_expected_cases()
    {
        $cases = Statuses::cases();
        $expectedCases = [
            'Partial',
            'InProgress',
            'Completed',
            'Pending',
            'Processing',
            'Cancelled',
        ];

        $this->assertCount(count($expectedCases), $cases);

        foreach ($expectedCases as $expectedCase) {
            $this->assertTrue(
                in_array($expectedCase, array_column($cases, 'name')),
                "Expected case {$expectedCase} not found"
            );
        }
    }

    public function test_order_status_mapping_for_partial()
    {
        $this->assertEquals(OrderStatuses::InProgress, Statuses::Partial->orderStatus());
    }

    public function test_order_status_mapping_for_in_progress()
    {
        $this->assertEquals(OrderStatuses::InProgress, Statuses::InProgress->orderStatus());
    }

    public function test_order_status_mapping_for_completed()
    {
        $this->assertEquals(OrderStatuses::Completed, Statuses::Completed->orderStatus());
    }

    public function test_order_status_mapping_for_pending()
    {
        $this->assertEquals(OrderStatuses::InProgress, Statuses::Pending->orderStatus());
    }

    public function test_order_status_mapping_for_processing()
    {
        $this->assertEquals(OrderStatuses::InProgress, Statuses::Processing->orderStatus());
    }

    public function test_order_status_mapping_for_cancelled()
    {
        $this->assertEquals(OrderStatuses::Cancelled, Statuses::Cancelled->orderStatus());
    }

    public function test_enum_to_array_trait_works()
    {
        $names = Statuses::names();
        $values = Statuses::values();
        $array = Statuses::array();

        $this->assertIsArray($names);
        $this->assertIsArray($values);
        $this->assertIsArray($array);
        $this->assertCount(count($names), $values);
        $this->assertCount(count($names), $array);
    }
}

