<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FieldResource\Pages;
use App\Filament\Resources\FieldResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Repeater;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FieldResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $modelLabel = 'Dynamic Ad Fields';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';


    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Placeholder::make('name')
                ->content(fn (Category $record): string => $record->name)
                ->label('Category Name'),
                Repeater::make('fields')
                ->label('Dynamic Fields')
                ->relationship()
                ->schema([
                    TextInput::make('name')
                    ->required(),
                    Select::make('type')
                    ->live()
                    ->required()
                    ->options([
                        'text' => 'Text',
                        'date' => 'Date',
                        'time' => 'Time',
                        'datetime' => 'Date Time',
                        'radio' => 'Radio',
                        'checkbox' => 'Checkbox',
                        'textarea' => 'Textarea',
                        'select' => 'Select',
                    ]),
                    Checkbox::make('required')->label('Is Required?'),
                    TagsInput::make('options')
                    ->visible(fn (Get $get): bool => in_array($get('type'), ['radio', 'select'])),
                ])
                ->columns(2)
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label('Category'),
                TextColumn::make('parent.name')
                ->label('Main Category')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->icon('heroicon-m-plus')
                ->label('Ad Fields'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNot('parent_id', null);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFields::route('/'),
            'edit' => Pages\EditField::route('/{record}/edit'),
        ];
    }
}
