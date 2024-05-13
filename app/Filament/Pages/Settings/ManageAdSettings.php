<?php

namespace App\Filament\Pages\Settings;

use App\Settings\AdSettings;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageAdSettings extends SettingsPage
{
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Ad';

    protected static string $settings = AdSettings::class;

    protected static ?int $navigationSort = 2;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(AdSettings::class);
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
        return $form
            ->schema([
           
            TextInput::make('ad_duration')
                ->numeric()
                ->minValue(1)
                ->maxValue(365)
                ->label('Default Duration for Ads (Days)')
                ->placeholder('Enter duration in days')
                ->required(),

            TextInput::make('image_limit')
                ->numeric()
                ->minValue(1)
                ->maxValue(10)
                ->label('Maximum Images Allowed Per Ad')
                ->placeholder('Enter a limit')
                ->required(),

            Checkbox::make('ad_moderation')
                ->label('Require Admin Approval for New Ads?'),
            ]);
    }
}
