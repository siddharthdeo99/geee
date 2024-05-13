<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AdSettings extends Settings
{
    public int $ad_duration;
    public int $image_limit;
    public bool $ad_moderation;
    public float $featured_ad_price;
    public int $featured_ad_duration;
    public float $spotlight_ad_price;
    public int $spotlight_ad_duration;
    public float $urgent_ad_price;
    public int $urgent_ad_duration;
    public int $free_ad_limit;
    public int $ad_renewal_days;

    public static function group(): string
    {
        return 'ad';
    }
}
