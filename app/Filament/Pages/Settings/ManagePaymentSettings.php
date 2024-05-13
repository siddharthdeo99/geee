<?php

namespace App\Filament\Pages\Settings;

use App\Settings\PaymentSettings;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Config;
use Akaunting\Money\Currency as AkauntingCurrency;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Artisan;
use Filament\Forms\Get;

class ManagePaymentSettings extends SettingsPage
{
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $title = 'Payment Settings';

    protected static ?string $navigationLabel = 'Payment';

    protected static string $settings = PaymentSettings::class;

    protected static ?int $navigationSort = 4;


    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(PaymentSettings::class);
        $filtered = [];

        foreach ($data as $key => $item) {
            // Check if the property exists in the GeneralSettings class
            if (property_exists($previousData, $key)) {
                // Get the type of the property
                $propertyType = gettype($previousData->$key);

                if ($key === 'currency') {
                    $currencySymbol = (new AkauntingCurrency($item))->getSymbol();
                    setEnvironmentValue('APP_CURRENCY', $currencySymbol);
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
         // Clear cache
         Artisan::call('config:clear');
        return $filtered;
    }

    public function form(Form $form): Form
    {
        $currenciesConfig = config('money.currencies');
        $currencyCodes = array_keys($currenciesConfig);
        return $form
            ->schema([
                Select::make('currency')
                ->label('Currency')
                ->options(array_combine($currencyCodes, $currencyCodes))
                ->searchable()
                ->placeholder('USD, EUR, etc.')
                ->required()
                ->helperText('Setting this will change the currency used across the entire website. Please proceed with caution.'),

                Toggle::make('enable_tax')
                    ->label('Enable Tax')
                    ->live()
                    ->columnSpanFull()
                    ->helperText('Toggle to enable or disable tax calculations.'),

                Select::make('tax_type')
                    ->label('Tax Type')
                    ->visible(fn (Get $get): bool => $get('enable_tax'))
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed'
                    ])
                    ->helperText('Choose how the tax is calculated. "Percentage" or "Fixed" amount.'),

                TextInput::make('tax_rate')
                    ->label('Tax Rate')
                    ->visible(fn (Get $get): bool => $get('enable_tax'))
                    ->numeric()
                    ->helperText('Enter the tax rate. Depending on the "Tax Type", this will be a percentage or a fixed amount.'),

                TextInput::make('exchange_rate')
                    ->label('Exchange Rate')
                    ->numeric()
                    ->helperText('Enter the exchange rate for currency conversion.'),

            ]);
    }
}
