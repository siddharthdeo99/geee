<?php

namespace App\Filament\Pages\Settings;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Config;

class ManageEmailSettings extends Page
{
    public ?array $data = [];

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Email';

    protected static string $view = 'filament.pages.settings.manage-email-settings';

    protected static ?int $navigationSort = 6;

    public function mount(): void
    {
        $this->data = [
            'smtp_host' => config('mail.mailers.smtp.host'),
            'smtp_port' => config('mail.mailers.smtp.port'),
            'smtp_password' => config('mail.mailers.smtp.password'),
            'smtp_user' => config('mail.mailers.smtp.username'),
            'from_email' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];
        $this->form->fill($this->data);
    }



    public function save()
    {
        try {
            $data = $this->form->getState();
            if (array_key_exists('smtp_host', $data)) {
                setEnvironmentValue('MAIL_HOST', $data['smtp_host']);
            }
            if (array_key_exists('smtp_user', $data)) {
                setEnvironmentValue('MAIL_USERNAME', $data['smtp_user']);
            }
            if (array_key_exists('smtp_password', $data)) {
                setEnvironmentValue('MAIL_PASSWORD', $data['smtp_password']);
            }
            if (array_key_exists('smtp_port', $data)) {
                setEnvironmentValue('MAIL_PORT', $data['smtp_port']);
            }
            if (array_key_exists('from_email', $data)) {
                setEnvironmentValue('MAIL_FROM_ADDRESS', $data['from_email']);
            }
            if (array_key_exists('from_name', $data)) {
                setEnvironmentValue('MAIL_FROM_NAME', $data['from_name']);
            }

            // Clear cache
            Artisan::call('config:clear');

            Notification::make()
                ->title('Saved.')
                ->success()
                ->send();
        } catch (\Throwable $th) {

            // Error
            Notification::make()
            ->title('Something went wrong.')
            ->danger()
            ->send();
            throw $th;

        }
    }

    public function form(Form $form): Form
    {
        $isDemo = Config::get('app.demo');

        return $form->schema([
            $isDemo ?
                Placeholder::make('smtp_host')
                ->label('SMTP Host')
                ->content('*****')
                ->hint('Due to demo account, values are hidden.') :
                TextInput::make('smtp_host')
                ->label('SMTP Host')
                ->placeholder('e.g., smtp.gmail.com')
                ->required(),

            $isDemo ?
                Placeholder::make('smtp_user')
                ->label('SMTP Username')
                ->content('*****')
                ->hint('Due to demo account, values are hidden.') :
                TextInput::make('smtp_user')
                ->label('SMTP Username')
                ->placeholder('Enter your SMTP username')
                ->required(),

            $isDemo ?
                Placeholder::make('smtp_password')
                ->label('SMTP Password')
                ->content('*****')
                ->hint('Due to demo account, values are hidden.') :
                TextInput::make('smtp_password')
                ->label('SMTP Password')
                ->placeholder('Enter your SMTP password')
                ->password()
                ->required(),

            $isDemo ?
                Placeholder::make('smtp_port')
                ->label('SMTP Port')
                ->content('*****')
                ->hint('Due to demo account, values are hidden.') :
                TextInput::make('smtp_port')
                ->numeric()
                ->label('SMTP Port')
                ->placeholder('e.g., 587 or 465')
                ->minValue(1)
                ->maxValue(65535)
                ->required(),

            $isDemo ?
                Placeholder::make('from_email')
                ->label('From Email Address')
                ->content('*****')
                ->hint('Due to demo account, values are hidden.') :
                TextInput::make('from_email')
                ->label('From Email Address')
                ->placeholder('e.g., noreply@yourdomain.com')
                ->email()
                ->required(),

            $isDemo ?
                Placeholder::make('from_name')
                ->label('From Name')
                ->content('*****')
                ->hint('Due to demo account, values are hidden.') :
                TextInput::make('from_name')
                ->label('From Name')
                ->placeholder('e.g., Your Company Name')
                ->required(),
        ])
        ->columns(2)
        ->statePath('data');
    }

}
