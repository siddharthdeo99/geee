<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionResource\Pages;
use App\Filament\Resources\PromotionResource\RelationManagers;
use App\Models\Promotion;
use App\Settings\PackageSettings;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static ?string $navigationGroup = 'Promotion Management';

    protected static ?string $navigationLabel = 'Promotion';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('name')
                ->content(fn (Promotion $record): string => $record->name)
                ->label('Promotion Name'),
                Placeholder::make('description')
                ->content(fn (Promotion $record): string => $record->description)
                ->label('Description'),
                TextInput::make('duration')
                ->suffix('Days')
                ->visible(!app(PackageSettings::class)->status)
                ->numeric(),
                TextInput::make('price')
                ->prefix(config('app.currency_symbol'))
                ->visible(!app(PackageSettings::class)->status)
                ->numeric()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label('Promotion Name'),
                TextColumn::make('description'),
                TextColumn::make('duration')->suffix(' days')->visible(!app(PackageSettings::class)->status),
                TextColumn::make('price')->prefix(config('app.currency_symbol'))->visible(!app(PackageSettings::class)->status)
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->visible(!app(PackageSettings::class)->status),
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
        return [
            'index' => Pages\ListPromotions::route('/'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}
