<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\OrderUpgrade;
use App\Settings\PackageSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = OrderUpgrade::class;

    protected static ?string $navigationGroup = 'Promotion Management';

    protected static ?string $navigationLabel = 'Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->latest())
            ->columns([
                TextColumn::make('id')
                    ->label('Order ID'),
                TextColumn::make('ad_title')
                ->label('Ad Name'),
                TextColumn::make('orderPromotions.adPromotion.promotion.name')
                    ->label('Promotion Type'),
                TextColumn::make('payment_method')
                    ->label('Payment Method'),
                TextColumn::make('taxes_value')
                    ->label('Tax'),
                TextColumn::make('subtotal_value')
                ->label('Sub Total'),
                TextColumn::make('total_value')
                ->label('Total'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date(),
                TextColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'completed',
                        'danger' => 'failed',
                        'warning' => 'refunded',
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return !app(PackageSettings::class)->status;
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
            'index' => Pages\ListTransactions::route('/'),
        ];
    }
}
