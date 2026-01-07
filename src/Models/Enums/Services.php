<?php

namespace Istoy\Models\Enums;

use Istoy\Traits\EnumToArray;

enum Services: string
{
    use EnumToArray;

    case HighQualityLikes = 'high_quality_likes';
    case PremiumQualityLikes = 'premium_quality_likes';
    case HighQualityViews = 'high_quality_views';
    case PremiumQualityViews = 'premium_quality_views';
    case Comments = 'comments';

    /**
     * External Id for the service
     *
     * @return array|null
     */
    public function id(): ?int
    {
        return match ($this) {
            self::HighQualityLikes => 1625,
            self::PremiumQualityLikes => 1688,
            self::HighQualityViews => 4939,
            self::PremiumQualityViews => 5980,
            self::Comments => 1518,
            default => null,
        };
    }

    /**
     * Likes Services
     *
     * @return array
     */
    public static function likes(): array
    {
        return [self::HighQualityLikes, self::PremiumQualityLikes];
    }

    /**
     * Views Services
     *
     * @return array
     */
    public static function views(): array
    {
        return [self::HighQualityViews, self::PremiumQualityViews];
    }

    /**
     * Comments Services
     *
     * @return array
     */
    public static function comments(): array
    {
        return [self::Comments];
    }

    /**
     * Minimum quantity for the service
     *
     * @return integer|null
     */
    public function minimum(): ?int
    {
        return match ($this) {
            self::HighQualityLikes => 20,
            self::PremiumQualityLikes => 10,
            self::HighQualityViews => 100,
            self::PremiumQualityViews => 1_000,
            self::Comments => 1,
            default => null,
        };
    }

    /**
     * Maximum quantity for the service
     *
     * @return integer|null
     */
    public function maximum(): ?int
    {
        return match ($this) {
            self::HighQualityLikes => 100_000,
            self::PremiumQualityLikes => 40_000,
            self::HighQualityViews => 100_000_000,
            self::PremiumQualityViews => 1_000_000,
            self::Comments => 5_000,
            default => null,
        };
    }

    public function type(): ?ServiceTypes
    {
        return match ($this) {
            self::HighQualityLikes => ServiceTypes::Likes,
            self::PremiumQualityLikes => ServiceTypes::Likes,
            self::HighQualityViews => ServiceTypes::Views,
            self::PremiumQualityViews => ServiceTypes::Views,
            self::Comments => ServiceTypes::Comments,
            default => null,
        };
    }

    public function description(): ?string
    {
        return match ($this) {
            self::HighQualityLikes => 'High-Quality Likes',
            self::PremiumQualityLikes => 'Premium-Quality LIkes',
            self::HighQualityViews => 'High-Quality Views',
            self::PremiumQualityViews => 'Premium-Quality Views',
            self::Comments => 'Comments',
            default => null,
        };
    }
}

