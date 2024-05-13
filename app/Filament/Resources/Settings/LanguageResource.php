<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\LanguageResource\Pages;
use App\Filament\Resources\Settings\LanguageResource\RelationManagers;
use App\Models\Country;
use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Languages';

    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                ->label('Language Title')
                ->required()
                ->maxLength(60)
                ->hint('Enter the full name of the language. E.g., "English".'),

                Forms\Components\TextInput::make('lang_code')
                    ->label('Language Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->hint('Enter the two-letter language code. E.g., "en" for English.'),

                Forms\Components\Select::make('country')
                    ->label('Country Code')
                    ->required()
                    ->options(Country::all()->pluck('name', 'code')),

                Forms\Components\Toggle::make('is_visible')
                    ->helperText('Toggle to enable or disable this language on your platform.'),

                Forms\Components\Toggle::make('rtl')
                    ->hidden()
                    ->helperText('Toggle ON if this language is written right-to-left (like Arabic). Otherwise, leave it OFF.')

            ]);
    }

    public static function table(Table $table): Table
    {
        $isDemo = Config::get('app.demo');
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lang_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_visible')
                    ->boolean(),
                Tables\Columns\IconColumn::make('rtl')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Manage Translations')
                ->hidden($isDemo)
                ->icon('heroicon-s-globe-alt')
                ->url(function ($record) {
                    return 'languages/' . $record->id . '/translate';
                }),
                Tables\Actions\EditAction::make()->hidden(fn (Language $record): bool => $record->id == 1 || $isDemo ),
                Tables\Actions\DeleteAction::make()->hidden(fn (Language $record): bool => $record->id == 1 || $isDemo )
                ->after(function (Language $record) {
                    // Language directory path
                    $langDir = lang_path(strtolower($record->lang_code));

                    // Check if directory exists
                    if (File::exists($langDir)) {
                        // Remove the directory and its contents
                        File::deleteDirectory($langDir);
                    }

                    // Refresh active languages
                    fetch_active_languages(true);
                })
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        $isDemo = Config::get('app.demo');

        return  $isDemo ? [
            'index' => Pages\ListLanguages::route('/'),
        ] :  [
            'index' => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'edit' => Pages\EditLanguage::route('/{record}/edit'),
            'translate' => Pages\TranslateLanguage::route('/{record}/translate'),
        ];
    }
}
