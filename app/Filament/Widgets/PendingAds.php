<?php

namespace App\Filament\Widgets;

use App\Models\Ad;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingAds extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;  // Sort order for the widget on the dashboard

    public static function canView(): bool
    {
        return Ad::where('status', 'pending')->count() > 0;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Ad::query()->where('status', 'pending'))
            ->defaultPaginationPageOption(5)
            ->defaultSort('posted_date', 'desc')
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
            ->actions([
                Action::make('view')
                ->icon('heroicon-o-eye')
                ->label('View Details')
                ->url(fn (Ad $record): string =>  route('ad-details', [
                    'slug' => $record->slug,
                    'admin_view' => 'true'
                ]))
                ->openUrlInNewTab()
            ]);
    }
}
