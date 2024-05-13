<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportedAdResource\Pages;
use App\Filament\Resources\ReportedAdResource\RelationManagers;
use App\Models\Ad;
use App\Models\ReportedAd;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Actions\Action;

class ReportedAdResource extends Resource
{
    protected static ?string $model = ReportedAd::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

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
            ->columns([
                TextColumn::make('id')
                ->label('Report ID'),
                TextColumn::make('user.name')
                ->label('Reported By'),
                TextColumn::make('reason')
                ->label('Reason'),
                TextColumn::make('created_at')
                ->label('Date Reported')
                ->date(),
                TextColumn::make('ad.user.name')
                ->label('Ad Owner'),
                SelectColumn::make('status')->options([
                    'pending' => 'Pending',
                    'seen' => 'Seen',
                ])->label('Change Status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('view')
                ->icon('heroicon-o-eye')
                ->label('View Ad Details')
                ->url(fn (ReportedAd $record): string =>  route('ad-details', [
                    'slug' => $record->ad->slug,
                    'admin_view' => 'true'
                ]))
                ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListReportedAds::route('/'),
            'create' => Pages\CreateReportedAd::route('/create'),
            'edit' => Pages\EditReportedAd::route('/{record}/edit'),
        ];
    }
}
