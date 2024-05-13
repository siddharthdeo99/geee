<?php

namespace App\Filament\Widgets;

use App\Models\User; // Use the correct namespace for your User model
use App\Models\Ad; // Use the correct namespace for your Ad model
use App\Models\AdPromotion;
use App\Models\Conversation; // Use the correct namespace for your Conversation model
use App\Models\OrderPackage;
use App\Models\OrderUpgrade;
use App\Settings\PackageSettings;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        // Fetch your stats from the database
        $totalUsers = User::count();
        $activeAds = Ad::where('status', 'active')->count();  // Replace 'status' and 'active' according to your database schema
        $adUpgradeRevenue = app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status ? OrderPackage::where('status', 'completed')->sum('total_value') : OrderUpgrade::where('status', 'completed')->sum('total_value');
        $userEngagement = Conversation::count(); // Replace with your logic for calculating user engagement

        return [
            Stat::make('Total Registered Users', $totalUsers)
                ->description('User base size')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),

            Stat::make('Active Ads', $activeAds)
                ->description('Number of active ads')
                ->descriptionIcon('heroicon-m-newspaper')
                ->chart([15, 4, 10, 2, 12, 4, 12])
                ->color('success'),

            Stat::make(app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status ? 'Packages Revenue' : 'Ad Upgrade Revenue', "$" . number_format($adUpgradeRevenue, 2))
                ->description(app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status ? 'Revenue from Packages' : 'Revenue from ad upgrades')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([16, 14, 12, 15, 17, 13, 10])
                ->color('info'),

            Stat::make('User Engagement', $userEngagement)
                ->description('User messages or interactions')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([14, 16, 15, 17, 12, 13, 10])
                ->color('warning'),
        ];
    }
}
