<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdResource\Pages;
use App\Filament\Resources\AdResource\RelationManagers;
use App\Models\Ad;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class AdResource extends Resource
{
    protected static ?string $model = Ad::class;

    protected static ?string $navigationLabel = 'Ad Management';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->latest())
            ->columns([
                SpatieMediaLibraryImageColumn::make('ads')
                ->collection('ads')
                ->conversion('thumb')
                ->defaultImageUrl(asset('images/placeholder.jpg'))
                ->label('Ad Images')
                ->size(40)
                ->circular()
                ->overlap(2)
                ->stacked()
                ->limit(3),
                TextColumn::make('title')
                ->searchable()
                ->label('Title'),
                TextColumn::make('user.name')
                ->label('Posted By')
                ->sortable(),
                TextColumn::make('price'),
                TextColumn::make('location_name')
                ->label('Location'),
                TextColumn::make('posted_date')->label('Posted On')->date(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                SelectColumn::make('status')->options([
                    'draft' => 'Draft',
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'sold' => 'Sold',
                ])->label('Change Status'),

            ])
            ->defaultSort('posted_date', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Action::make('view')
                ->icon('heroicon-o-eye')
                ->label('View Details')
                ->url(fn (Ad $record): string =>  route('ad-details', [
                    'slug' => $record->slug,
                    'admin_view' => 'true'
                ]))
                ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListAds::route('/'),
        ];
    }
}
