<?php
namespace App\Settings;

use Spatie\LaravelSettings\Settings;


class GeneralSettings extends Settings
{

    public bool $setup_finished;
    public string $purchase_code;
    public string $site_name;
    public string $separator;
    public string $site_tagline;
    public string $site_description;
    public string $logo_path;
    public string $favicon_path;
    public string $default_language;
    public string $contact_email;
    public string $contact_phone;
    public string $contact_address;
    public bool $cookie_consent_enabled;
    public string $cookie_consent_message;
    public string $cookie_consent_agree;
    public float $logo_height_mobile;
    public float $logo_height_desktop;

    public static function group(): string
    {
        return 'general';
    }
}
