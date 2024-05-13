<?php

namespace App\Filament\Pages\Settings;

use App\Settings\ScriptSettings;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Config;

class ManageScriptSettings extends SettingsPage
{
    protected static ?string $title = 'Script Settings';

    protected static string $settings = ScriptSettings::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Scripts';

    protected static ?int $navigationSort = 8;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(ScriptSettings::class);
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
                Textarea::make('custom_script_head')
                    ->label('Custom Script for Head')
                    ->placeholder('<script>// Your script for head here...</script>')
                    ->rows(5)
                    ->disabled($isDemo)
                    ->hint($isDemo ? 'Due to demo account, you are not allowed to edit here.' :'Paste the script that you want to inject into the head of the pages.'),

                Textarea::make('custom_script_body')
                    ->label('Custom Script for Body')
                    ->placeholder('<script>// Your script for body here...</script>')
                    ->rows(5)
                    ->disabled($isDemo)
                    ->hint($isDemo ? 'Due to demo account, you are not allowed to edit here.' :'Paste the script that you want to inject into the body of the pages.'),
            ])
            ->columns(1);
    }
}
