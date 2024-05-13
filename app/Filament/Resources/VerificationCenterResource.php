<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerificationCenterResource\Pages;
use App\Filament\Resources\VerificationCenterResource\RelationManagers;
use App\Models\VerificationCenter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\HtmlString;
use Filament\Forms\Get;

class VerificationCenterResource extends Resource
{
    protected static ?string $model = VerificationCenter::class;

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Verification';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('document_type')
                    ->label('Document Type')
                    ->required()
                    ->options([
                        'id' => __('messages.t_government_issued_id'),
                        'driving' => __('messages.t_driver_license'),
                        'passport' => __('messages.t_passport')
                    ]),
                Grid::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('front_side')
                            ->label('Document Front Side')
                            ->collection('front_side_verification')
                            ->visibility('private')
                            ->image()
                            ->disabled()
                            ->downloadable(),
                        SpatieMediaLibraryFileUpload::make('back_side')
                            ->label('Document Back Side')
                            ->collection('back_side_verification')
                            ->visibility('private')
                            ->image()
                            ->disabled()
                            ->downloadable(),
                        SpatieMediaLibraryFileUpload::make('download')
                            ->label('Download Selfie')
                            ->disabled()
                            ->collection('selfie')
                            ->downloadable()
                    ]),
                Grid::make()
                ->schema([
                    Select::make('status')->options([
                            'pending' => 'Pending',
                            'declined' => 'Declined',
                            'verified' => 'Verified',
                        ])->label('Status'),
                ]),
                Textarea::make('comments')
                    ->label('Comments')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                ->label('User'),
                TextColumn::make('document_type')
                ->formatStateUsing(function (string $state): string {
                    switch ($state) {
                        case 'id':
                            return __('messages.t_government_issued_id');
                        case 'driving':
                            return __('messages.t_driver_license');
                        case 'passport':
                            return __('messages.t_passport');
                        default:
                            return ucfirst($state);
                    }
                })
                ->label('Document Type'),
                SpatieMediaLibraryImageColumn::make('selfie')
                ->label('Selfie')
                ->collection('selfie')
                ->defaultImageUrl(asset('images/placeholder.jpg'))
                ->conversion('thumb')
                ->visibility('private')
                ->size(40),
                SpatieMediaLibraryImageColumn::make('front_side')
                ->label('Front Side')
                ->collection('front_side_verification')
                ->defaultImageUrl(asset('images/placeholder.jpg'))
                ->conversion('thumb')
                ->visibility('private')
                ->size(40),
                SpatieMediaLibraryImageColumn::make('back_side')
                ->label('Back Side')
                ->collection('back_side_verification')
                ->defaultImageUrl(asset('images/placeholder.jpg'))
                ->conversion('thumb')
                ->visibility('private')
                ->size(40),
                SelectColumn::make('status')->options([
                    'pending' => 'Pending',
                    'declined' => 'Declined',
                    'verified' => 'Verified',
                ])->label('Change Status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->label('View details'),
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
            'index' => Pages\ListVerificationCenters::route('/'),
            'create' => Pages\CreateVerificationCenter::route('/create'),
            'edit' => Pages\EditVerificationCenter::route('/{record}/edit'),
        ];
    }
}
