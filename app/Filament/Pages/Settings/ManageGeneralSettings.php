<?php

namespace App\Filament\Pages\Settings;

use App\Models\Language;
use App\Models\SettingsProperty;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\SettingsPage;
use Config;

class ManageGeneralSettings extends SettingsPage
{
    protected static ?string $title = 'General Settings';

    protected static string $settings = GeneralSettings::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'General';

    protected static ?int $navigationSort = 1;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(GeneralSettings::class);
        $filtered = [];

        foreach ($data as $key => $item) {
            // Check if the property exists in the GeneralSettings class
            if (property_exists($previousData, $key)) {
                // Get the type of the property
                $propertyType = gettype($previousData->$key);
                if ($key === 'site_name') {
                    Config::write('app.name', $item);
                }

                if ($key === 'default_language') {
                    Config::write('app.locale', $item);
                    Config::write('app.fallback_locale', $item);
                }


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
                TextInput::make('site_name')
                    ->label('Site Name')
                    ->placeholder('Name of your website')
                    ->required(),

                TextInput::make('separator')
                    ->label('Separator for Titles')
                    ->placeholder('e.g., | or -')
                    ->helperText('This character will be used to separate the site name and page title in browser tabs.'),

                TextInput::make('site_tagline')
                    ->label('Site Tagline')
                    ->placeholder('A catchy phrase or tagline for your website')
                    ->required(),



                Textarea::make('site_description')
                    ->label('Site Description')
                    ->required()
                    ->columnSpanFull(),

                    Grid::make(5)
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('logo_path')
                                ->label('Upload Logo')
                                ->collection('logo')
                                ->columnSpan(3)
                                ->image()
                                ->model(
                                    SettingsProperty::getInstance('general.logo_path'),
                                )
                                ->hint('Upload the logo for your website'),

                            TextInput::make('logo_height_mobile')
                                ->label('Mobile Logo Height')
                                ->numeric()
                                ->columnSpan(1)
                                ->inputMode('decimal')
                                ->placeholder('Enter logo height for mobile (e.g., 1.5)')
                                ->helperText('Specify the height of the logo on mobile devices.'),

                            TextInput::make('logo_height_desktop')
                                ->label('Desktop Logo Height')
                                ->numeric()
                                ->columnSpan(1)
                                ->inputMode('decimal')
                                ->placeholder('Enter logo height for desktop (e.g., 2)')
                                ->helperText('Specify the height of the logo on desktop devices.'),
                ]),

                SpatieMediaLibraryFileUpload::make('favicon_path')
                    ->label('Upload Favicon')
                    ->collection('favicon')
                    ->image()
                    ->model(
                        SettingsProperty::getInstance('general.favicon_path'),
                    )
                    ->hint('Upload the favicon for your website.'),

                Select::make('default_language')
                    ->label('Default Language')
                    ->options(Language::all()->pluck('title', 'lang_code'))
                    ->placeholder('Select a language')
                    ->hint('The default language used across the site.'),
                Grid::make()
                    ->schema([
                        TextInput::make('contact_email')
                            ->label('Contact Email')
                            ->email()
                            ->placeholder('e.g., info@yourdomain.com'),

                        TextInput::make('contact_phone')
                            ->label('Contact Phone')
                            ->tel()
                            ->placeholder('e.g., +1 123 456 7890'),
                    ]),


                Textarea::make('contact_address')
                    ->label('Contact Address')
                    ->placeholder('Enter your contact address.')
                    ->rows(3),
                Grid::make()
                    ->schema([
                        Toggle::make('cookie_consent_enabled')
                            ->label('Enable Cookie Consent')
                            ->live(),

                        Textarea::make('cookie_consent_message')
                            ->label('Cookie Consent Message')
                            ->visible(fn (Get $get): bool => $get('cookie_consent_enabled'))
                            ->placeholder('The message displayed in the cookie consent popup.')
                            ->rows(4),

                        TextInput::make('cookie_consent_agree')
                            ->label('Cookie Consent Agree Button Text')
                            ->visible(fn (Get $get): bool => $get('cookie_consent_enabled'))
                            ->placeholder('Text for the "Agree" button.')
                    ]),
            ]);
    }
}
