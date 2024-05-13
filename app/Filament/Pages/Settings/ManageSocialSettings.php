<?php

namespace App\Filament\Pages\Settings;

use App\Settings\SocialSettings;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageSocialSettings extends SettingsPage
{
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Social';

    protected static string $settings = SocialSettings::class;

    protected static ?int $navigationSort = 10;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(SocialSettings::class);
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
                TextInput::make('facebook_link')
                    ->label('Facebook Link')
                    ->placeholder('Enter the URL for your Facebook profile')
                    ->url()
                    ->hint('Your Facebook profile link.'),

                TextInput::make('twitter_link')
                    ->label('Twitter Link')
                    ->placeholder('Enter the URL for your Twitter profile')
                    ->url()
                    ->hint('Your Twitter profile link.'),

                TextInput::make('instagram_link')
                    ->label('Instagram Link')
                    ->placeholder('Enter the URL for your Instagram profile')
                    ->url()
                    ->hint('Your Instagram profile link.'),

                TextInput::make('linkedin_link')
                    ->label('LinkedIn Link')
                    ->placeholder('Enter the URL for your LinkedIn profile')
                    ->url()
                    ->hint('Your LinkedIn profile link.'),
            ]);
    }
}
