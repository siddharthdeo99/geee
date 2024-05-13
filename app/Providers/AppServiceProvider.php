<?php

namespace App\Providers;

use App\Settings\AdPlacementSettings;
use App\Settings\AdSettings;
use App\Settings\GeneralSettings;
use App\Settings\LocationSettings;
use App\Settings\PaymentSettings;
use App\Settings\ScriptSettings;
use App\Settings\AuthSettings;
use App\Settings\ReCaptchaConfig;
use App\Settings\SocialSettings;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Illuminate\Mail\MailManager;
use Akaunting\Money\Currency as AkauntingCurrency;
use App\Models\Message;
use App\Observers\MessageObserver;
use App\Settings\BlogSettings;
use App\Settings\PackageSettings;
use TimeHunter\LaravelGoogleReCaptchaV3\Interfaces\ReCaptchaConfigV3Interface;
use App\Settings\LoginOtpSettings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(
            ReCaptchaConfigV3Interface::class,
            ReCaptchaConfig::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(MailManager $mailManager): void
    {
        DatabaseNotifications::trigger('notifications.database-notifications-trigger');
        Message::observe(MessageObserver::class);
        View::share('generalSettings', app(GeneralSettings::class));
        View::share('adSettings', app(AdSettings::class));
        View::share('locationSettings', app(LocationSettings::class));
        View::share('paymentSettings', app(PaymentSettings::class));
        View::share('scriptSettings', app(ScriptSettings::class));
        View::share('adPlacementSettings', app(AdPlacementSettings::class));
        View::share('authSettings', app(AuthSettings::class));
        View::share('socialSettings', app(SocialSettings::class));
        View::share('packageSettings', app(PackageSettings::class));
        View::share('blogSettings', app(BlogSettings::class));
        View::share('loginOtpSettings', app(LoginOtpSettings::class));
    }
}
