<?php

namespace App\Filament\Pages\System;

use Illuminate\Support\Facades\Auth;
use App\Notifications\Admin\SiteIsDown;
use App\Settings\MaintenanceSettings;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Config;
use Illuminate\Support\HtmlString;


class Maintenance extends SettingsPage
{
    protected static ?string $navigationGroup = 'System Manager';

    protected static ?string $navigationLabel = 'Maintenance';

    protected ?string $heading = 'Maintenance Mode';

    protected ?string $subheading = 'Please proceed with caution. Copy the secret URL for site access during maintenance. This URL will also be emailed for secure entry.';

    protected static string $settings = MaintenanceSettings::class;

    protected static ?int $navigationSort = 20;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Check if the application is in maintenance mode
        $data['maintenance_mode'] = app()->isDownForMaintenance() ? 'maintenance' : 'live';
        if ($data['maintenance_mode'] === 'live') {
            // Generate a new secret
            $secret = (string) Str::uuid()->toString();
            $data['secret'] = $secret;
        }

        return $data;
    }


    protected function afterSave(): void
    {

        $data = $this->form->getState();


        // Handle maintenance mode activation/deactivation
        if ($data['maintenance_mode'] === 'maintenance') {

            // Set maintenance mode settings
            Config::write('maintenance.headline', str_replace(["'", '"'], '', $data['headline']));
            Config::write('maintenance.message', str_replace(["'", '"'], '', $data['message']));
            Config::write('maintenance.secret', $data['secret']);

             // Notify the logged-in user
             $user = Auth::user();
             if ($user) {
                 $user->notify(new SiteIsDown($data['secret']));
             }

            // Put site in maintenance mode with the new secret
            Artisan::call('down', ['--secret' => $data['secret']]);

        } else if ($data['maintenance_mode'] === 'live') {
            // Bring site back up
            Artisan::call('up');
        }

        // Clear config cache if needed
        Artisan::call('config:clear');
    }


    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(MaintenanceSettings::class);
        $filtered = [];

        foreach ($data as $key => $item) {
            if (property_exists($previousData, $key)) {
                $propertyType = gettype($previousData->$key);

                if (is_null($item) && $propertyType === 'string') {
                    $filtered[$key] = '';
                    continue;
                }
            }
            $filtered[$key] = $item;
        }
        return $filtered;
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('maintenance_mode')
                ->label('Application Status')
                ->options([
                    'live' => 'Application is Live',
                    'maintenance' => 'Application Under Maintenance',
                ])
                ->required()
                ->hint('Select whether the application is live or under maintenance.'),

                TextInput::make('headline')
                    ->label('Headline')
                    ->placeholder('Enter the headline for maintenance mode')
                    ->required(),

                Textarea::make('message')
                    ->label('Message')
                    ->placeholder('Enter the maintenance message')
                    ->required(),

                TextInput::make('secret')
                    ->label('Secret Key')
                    ->placeholder('Secret key for maintenance mode')
                    ->readOnly(),

                Placeholder::make('maintenanceUrl')
                    ->label('Maintenance URL')
                    ->content(function (Get $get): string {
                        $secret = $get('secret');
                        return $secret ?  url('/') . "/{$secret}" : 'Access URL will appear here after generating a secret key';
                    })
                    ->columnSpanFull()
                    ->helperText(new HtmlString('After the site is put into maintenance mode, use this URL with the secret key to access the site. <strong>Note:</strong> Only use this in emergency or maintenance situations.'))
            ]);
    }
}
