<?php

namespace App\Filament\Pages\Settings;

use App\Settings\AuthSettings;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Config;
use Filament\Forms\Get;

class ManageAuthSettings extends SettingsPage
{
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Auth';

    protected static string $settings = AuthSettings::class;

    protected static ?int $navigationSort = 11;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['recaptcha_site_key'] = config('recaptcha.site_key');
        $data['recaptcha_secret_key'] = config('recaptcha.secret_key');

        $data['google_client_id'] = config('google.client_id');
        $data['google_client_secret'] = config('google.client_secret');

        return $data;
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(AuthSettings::class);
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

            // If the key is recaptcha_site_key or recaptcha_secret_key, write it to config
            if ($key === 'recaptcha_site_key') {
                setEnvironmentValue('RECAPTCHA_SITE_KEY', $item);
            } elseif ($key === 'recaptcha_secret_key') {
                setEnvironmentValue('RECAPTCHA_SECRET_KEY', $item);
            } elseif ($key === 'google_client_id') {
                setEnvironmentValue('GOOGLE_CLIENT_ID', $item);
            } elseif ($key === 'google_client_secret') {
                setEnvironmentValue('GOOGLE_CLIENT_SECRET', $item);
            }
        }
        return $filtered;
    }

    public function form(Form $form): Form
    {
        $isDemo = Config::get('app.demo');
        return $form
            ->schema([
                Toggle::make('recaptcha_enabled')
                ->label('Enable  Google reCAPTCHA')
                ->live()
                ->columnSpanFull(),

                $isDemo ?
                    Placeholder::make('recaptcha_site_key')
                        ->content('*****')
                        ->visible(fn (Get $get): bool => $get('recaptcha_enabled'))
                        ->hint('Hidden due to demo mode.') :
                    TextInput::make('recaptcha_site_key')
                        ->label('reCAPTCHA Site Key')
                        ->placeholder('Enter your reCAPTCHA Site Key')
                        ->visible(fn (Get $get): bool => $get('recaptcha_enabled'))
                        ->required()
                        ->hint('Your Google reCAPTCHA Site Key for added security.'),

                $isDemo ?
                    Placeholder::make('recaptcha_secret_key')
                        ->content('*****')
                        ->visible(fn (Get $get): bool => $get('recaptcha_enabled'))
                        ->hint('Hidden due to demo mode.') :
                    TextInput::make('recaptcha_secret_key')
                        ->label('reCAPTCHA Secret Key')
                        ->placeholder('Enter your reCAPTCHA Secret Key')
                        ->visible(fn (Get $get): bool => $get('recaptcha_enabled'))
                        ->required()
                        ->hint('Your Google reCAPTCHA Secret Key for added security.'),


                     Grid::make()
                        ->schema([
                            Toggle::make('enable_google_login')
                                ->label('Enable Google Login')
                                ->live()
                                ->columnSpanFull(),

                            $isDemo ?
                            Placeholder::make('google_client_id')
                            ->content('*****')
                            ->visible(fn (Get $get): bool => $get('enable_google_login'))
                            ->hint('Hidden due to demo mode.') :
                            TextInput::make('google_client_id')
                                ->label('Google Client ID')
                                ->visible(fn (Get $get): bool => $get('enable_google_login')),
                            $isDemo ? Placeholder::make('google_client_secret')
                            ->content('*****')
                            ->visible(fn (Get $get): bool => $get('enable_google_login'))
                            ->hint('Hidden due to demo mode.') :
                            TextInput::make('google_client_secret')
                                ->label('Google Client Secret')
                                ->visible(fn (Get $get): bool => $get('enable_google_login'))
                        ]),

            ]);
    }
}
