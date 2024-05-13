<?php

namespace App\Providers\Filament;

use App\Settings\GeneralSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            ->bootUsing(function (Panel $panel) {
                try {
                    $faviconPath = getSettingMediaUrl('general.favicon_path', 'favicon', asset('images/favicon.png'));
                    $panel->favicon($faviconPath);

                } catch (\Exception $e) {
                    $panel->favicon(asset('images/favicon.png'));
                }
            })
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Slate,
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->authGuard('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->font('DM Sans', provider: GoogleFontProvider::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\PageViewsWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\VisitorsWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersOneDayWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersSevenDayWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsDurationWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsByCountryWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsByDeviceWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\MostVisitedPagesWidget::class,
                \BezhanSalleh\FilamentGoogleAnalytics\Widgets\TopReferrersListWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                ->label('User Management')
                ->icon('heroicon-o-users'),
                NavigationGroup::make()
                     ->label('Promotion Management')
                     ->icon('heroicon-o-ticket'),
                NavigationGroup::make()
                     ->label('Package Management')
                     ->icon('heroicon-o-cube'),
                NavigationGroup::make()
                     ->label('Blog')
                     ->icon('heroicon-o-document-text'),
                NavigationGroup::make()
                     ->label('Settings')
                     ->icon('heroicon-o-cog-6-tooth'),
                NavigationGroup::make()
                     ->label('Payment Gateways')
                     ->icon('heroicon-o-banknotes'),
                NavigationGroup::make()
                     ->label('System Manager')
                     ->icon('heroicon-o-wrench-screwdriver'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->passwordReset()
            ->profile()
            ->renderHook(
                name: 'panels::user-menu.before',
                hook: fn (): string => Blade::render('@livewire(\'admin.system.cache-manager\')')
            );
    }
}
