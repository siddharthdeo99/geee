<?php

namespace App\Filament\Pages\Settings;

use App\Settings\AdPlacementSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Config;

class ManageAdPlacementSettings extends SettingsPage
{
    protected static ?string $title = 'Ad Placement Settings';

    protected static string $settings = AdPlacementSettings::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Ad Placements';

    protected static ?int $navigationSort = 7;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(AdPlacementSettings::class);
        $filtered = [];

        foreach ($data as $key => $item) {
            // Check if the property exists in the GeneralSettings class
            if (property_exists($previousData, $key)) {
                // Get the type of the property
                $propertyType = gettype($previousData->$key);

                // If the item is null and the property type is string, set it to an empty string
                if (is_null($item) && $propertyType === 'string') {
                    $filtered[$key] = '';
                    continue;
                }
            }
            // For other cases, just copy the item as is
            $filtered[$key] = $item;
        }
        return $filtered;
    }
    public function form(Form $form): Form
    {
        $isDemo = Config::get('app.demo');
        return $form
            ->schema([
                Textarea::make('after_header')
                    ->label('Ad Code After Header')
                    ->placeholder('// Your ad code for after header here...')
                    ->rows(5)
                    ->default('Testing')
                    ->disabled($isDemo)
                    ->hint($isDemo ? 'Due to demo account, you are not allowed to edit here.' : 'Paste the ad code that you want to display after the header on the page.'),

                Textarea::make('before_footer')
                    ->label('Ad Code Before Footer')
                    ->placeholder('// Your ad code for before footer here...')
                    ->rows(5)
                    ->default('')
                    ->disabled($isDemo)
                    ->hint($isDemo ? 'Due to demo account, you are not allowed to edit here.' : 'Paste the ad code that you want to display before the footer on the page.'),
            ])
            ->columns(1);
    }
}
