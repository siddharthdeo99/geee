<?php

namespace App\Filament\Pages\Settings;

use App\Settings\LocationSettings;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Config;
use App\Models\Country;

class ManageLocation extends SettingsPage
{
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Ad Location';

    protected static string $settings = LocationSettings::class;

    protected static ?int $navigationSort = 3;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['google_api_key'] = config('google.api_key');

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(LocationSettings::class);
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
            // If the key is stripe_api_key or stripe_secret_key, write it to config
            if ($key === 'google_api_key') {
                setEnvironmentValue('GOOGLE_API_KEY', $item);
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
                $isDemo ?
                Placeholder::make('google_api_key')
                    ->content('*****')
                    ->hint('Hidden due to demo mode.') :
                TextInput::make('google_api_key')
                    ->label('Google Maps API Key')
                    ->placeholder('Enter your Google Maps API Key')
                    ->helperText('This key is essential for location-based services, such as location tagging and search functionalities. Make sure the Maps JavaScript API is enabled for your API key in the Google Cloud Console.')
                    ->required()
                    ->hidden(true),

                Select::make('allowed_countries')
                ->multiple()
                ->label('Allowed Countries')
                ->searchable()
                ->options(Country::all()->pluck('name', 'code'))
                ->hint('Countries that are allowed to access the marketplace.'),

                Select::make('default_country')
                    ->label('Default Country')
                    ->searchable()
                    ->options(Country::all()->pluck('name', 'code'))
                    ->placeholder('Select a default country')
                    ->hint('The default country used for the marketplace.'),

                TextInput::make('search_radius')
                    ->numeric()
                    ->label('Search Radius (km)')
                    ->placeholder('Enter the default search radius in kilometers')
                    ->helperText('This value represents the default radius (in km) for location-based searches.')
                    ->required(),

            ]);
    }
}
