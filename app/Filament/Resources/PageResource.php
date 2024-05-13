<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $recordId = request()->route('record');

        $restrictedIds = [1, 2, 3, 4];
        $shouldHide = in_array($recordId, $restrictedIds);

        return $form
            ->schema([
                TextInput::make('title')
                ->required(),
                MarkdownEditor::make('content')
                ->required(),
                Section::make('Search Engine Preview')
                ->description('Write a short description to see how this page may show up in search engine results.')
                ->schema([
                    TextInput::make('seo_title')
                    ->label('SEO Title'),
                    TextInput::make('seo_description')
                    ->label('SEO Description'),
                    TextInput::make('slug')
                    ->label('Page URL')
                    ->unique(ignoreRecord: true)
                    ->disabled($shouldHide)
                    ->required(),
                ])
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                ->label('Title'),
                TextColumn::make('slug')
                ->label('Page URL'),
                SelectColumn::make('status')->options([
                    'visible' => 'Published',
                    'hidden' => 'Draft',
                ])
                ->label('Change Status')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
