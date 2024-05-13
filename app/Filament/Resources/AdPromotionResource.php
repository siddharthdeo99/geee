<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdPromotionResource\Pages;
use App\Filament\Resources\AdPromotionResource\RelationManagers;
use App\Models\Ad;
use App\Models\AdPromotion;
use App\Models\Promotion;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdPromotionResource extends Resource
{
    protected static ?string $model = AdPromotion::class;

    protected static ?string $navigationGroup = 'Promotion Management';

    protected static ?string $navigationLabel = 'Promoted Ads';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('ad_id')
                ->label('Ad Title')
                ->options(Ad::all()->pluck('title', 'id')),
                Select::make('promotion_id')
                ->label('Promotion Type')
                ->options(Promotion::all()->pluck('name', 'id')),
                TextInput::make('price')
                ->prefix(config('app.currency_symbol'))
                ->numeric(),
                DatePicker::make('start_date')->native(false),
                DatePicker::make('end_date')->native(false)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->latest())
            ->columns([
                TextColumn::make('ad.title')
                ->label('Ad Name')
                ->searchable(),
                TextColumn::make('promotion.name')
                    ->label('Promotion Type')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Promotion Start Date')
                    ->date(),
                TextColumn::make('end_date')
                    ->label('Promotion End Date')
                    ->date(),
                TextColumn::make('price')
                    ->label('Price'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListAdPromotions::route('/'),
            'create' => Pages\CreateAdPromotion::route('/create'),
            'edit' => Pages\EditAdPromotion::route('/{record}/edit'),
        ];
    }
}
