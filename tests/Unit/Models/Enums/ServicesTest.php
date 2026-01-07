<?php

namespace Istoy\Tests\Unit\Models\Enums;

use Istoy\Tests\TestCase;
use Istoy\Models\Enums\Services;
use Istoy\Models\Enums\ServiceTypes;

class ServicesTest extends TestCase
{
    public function test_services_enum_has_all_expected_cases()
    {
        $cases = Services::cases();
        $expectedCases = [
            'HighQualityLikes',
            'PremiumQualityLikes',
            'HighQualityViews',
            'PremiumQualityViews',
            'Comments',
        ];

        $this->assertCount(count($expectedCases), $cases);

        foreach ($expectedCases as $expectedCase) {
            $this->assertTrue(
                in_array($expectedCase, array_column($cases, 'name')),
                "Expected case {$expectedCase} not found"
            );
        }
    }

    public function test_service_ids_are_correct()
    {
        $this->assertEquals(1625, Services::HighQualityLikes->id());
        $this->assertEquals(1688, Services::PremiumQualityLikes->id());
        $this->assertEquals(4939, Services::HighQualityViews->id());
        $this->assertEquals(5980, Services::PremiumQualityViews->id());
        $this->assertEquals(1518, Services::Comments->id());
    }

    public function test_minimum_quantities_are_correct()
    {
        $this->assertEquals(20, Services::HighQualityLikes->minimum());
        $this->assertEquals(10, Services::PremiumQualityLikes->minimum());
        $this->assertEquals(100, Services::HighQualityViews->minimum());
        $this->assertEquals(1_000, Services::PremiumQualityViews->minimum());
        $this->assertEquals(1, Services::Comments->minimum());
    }

    public function test_maximum_quantities_are_correct()
    {
        $this->assertEquals(100_000, Services::HighQualityLikes->maximum());
        $this->assertEquals(40_000, Services::PremiumQualityLikes->maximum());
        $this->assertEquals(100_000_000, Services::HighQualityViews->maximum());
        $this->assertEquals(1_000_000, Services::PremiumQualityViews->maximum());
        $this->assertEquals(5_000, Services::Comments->maximum());
    }

    public function test_likes_services_returns_correct_services()
    {
        $likesServices = Services::likes();
        
        $this->assertCount(2, $likesServices);
        $this->assertContains(Services::HighQualityLikes, $likesServices);
        $this->assertContains(Services::PremiumQualityLikes, $likesServices);
    }

    public function test_views_services_returns_correct_services()
    {
        $viewsServices = Services::views();
        
        $this->assertCount(2, $viewsServices);
        $this->assertContains(Services::HighQualityViews, $viewsServices);
        $this->assertContains(Services::PremiumQualityViews, $viewsServices);
    }

    public function test_comments_services_returns_correct_services()
    {
        $commentsServices = Services::comments();
        
        $this->assertCount(1, $commentsServices);
        $this->assertContains(Services::Comments, $commentsServices);
    }

    public function test_service_types_are_correct()
    {
        $this->assertEquals(ServiceTypes::Likes, Services::HighQualityLikes->type());
        $this->assertEquals(ServiceTypes::Likes, Services::PremiumQualityLikes->type());
        $this->assertEquals(ServiceTypes::Views, Services::HighQualityViews->type());
        $this->assertEquals(ServiceTypes::Views, Services::PremiumQualityViews->type());
        $this->assertEquals(ServiceTypes::Comments, Services::Comments->type());
    }

    public function test_service_descriptions_are_correct()
    {
        $this->assertEquals('High-Quality Likes', Services::HighQualityLikes->description());
        $this->assertEquals('Premium-Quality LIkes', Services::PremiumQualityLikes->description());
        $this->assertEquals('High-Quality Views', Services::HighQualityViews->description());
        $this->assertEquals('Premium-Quality Views', Services::PremiumQualityViews->description());
        $this->assertEquals('Comments', Services::Comments->description());
    }

    public function test_enum_to_array_trait_works()
    {
        $names = Services::names();
        $values = Services::values();
        $array = Services::array();

        $this->assertIsArray($names);
        $this->assertIsArray($values);
        $this->assertIsArray($array);
        $this->assertCount(count($names), $values);
        $this->assertCount(count($names), $array);
    }
}

