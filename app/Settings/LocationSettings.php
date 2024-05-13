<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LocationSettings extends Settings
{

    public array $allowed_countries;
    public string $default_country;
    public array $allowed_states;
    public int $search_radius;

    public static function group(): string
    {
        return 'location';
    }
}
