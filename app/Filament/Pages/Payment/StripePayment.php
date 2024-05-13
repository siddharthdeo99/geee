<?php

namespace App\Filament\Pages\Payment;

use App\Settings\StripeSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Models\SettingsProperty;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Facades\Config;

class StripePayment extends SettingsPage
{
    protected static ?string $title = 'Stripe Settings';

    protected static ?string $navigationGroup = 'Payment Gateways';

    protected static ?string $navigationLabel = 'Stripe';

    protected static ?string $slug = 'manage-stripe-settings';

    protected static string $settings = StripeSettings::class;

    protected static ?int $navigationSort = 10;


    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(StripeSettings::class);
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
        $currenciesConfig = config('money.currencies');
        $currencyCodes = array_keys($currenciesConfig);
        $isDemo = Config::get('app.demo');
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Stripe Name')
                    ->required()
                    ->helperText('Name of the Stripe configuration.'),

                Toggle::make('status')
                    ->label('Enable Stripe')
                    ->helperText('Toggle to enable or disable Stripe integration.'),

                SpatieMediaLibraryFileUpload::make('logo')
                    ->label('Stripe Logo')
                    ->collection('stripe')
                    ->hidden()
                    ->visibility('public')
                    ->image()
                    ->model(
                        SettingsProperty::getInstance('stripe.logo'),
                    )
                    ->helperText('Upload the logo for Stripe integration.'),

                Select::make('currency')
                    ->label('Default Currency')
                    ->options(array_combine($currencyCodes, $currencyCodes))
                    ->required()
                    ->helperText('Specify the default currency for processing payments through Stripe.'),


                $isDemo ?
                Placeholder::make('public_key')
                    ->label('Stripe Public Key')
                    ->content('*****')
                    ->hint('Hidden due to demo mode.') :
                TextInput::make('public_key')
                    ->label('Stripe Public Key')
                    ->required()
                    ->helperText('Your Stripe Public Key.'),

                $isDemo ?
                Placeholder::make('secret_key')
                    ->label('Stripe Secret Key')
                    ->content('*****')
                    ->hint('Hidden due to demo mode.') :
                TextInput::make('secret_key')
                    ->label('Stripe Secret Key')
                    ->required()
                    ->helperText('Your Stripe Secret Key.'),

                TextInput::make('exchange_rate')
                    ->label('Exchange Rate')
                    ->numeric()
                    ->helperText('Provide the exchange rate to be used for converting transaction amounts to the default currency. '),                    ])
                    ->columns(2);
    }
}
