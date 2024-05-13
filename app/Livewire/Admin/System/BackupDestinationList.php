<?php

namespace App\Livewire\Admin\System;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\BackupDestination;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination as SpatieBackupDestination;

class BackupDestinationList extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    /**
     * @var array<int|string, array<string, string>|string>
     */
    protected $queryString = [
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
    ];

    public function render(): View
    {
        return view('livewire.admin.system.backup-destination-list');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(BackupDestination::query())
            ->columns([
                Tables\Columns\TextColumn::make('path')
                    ->label('Path')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('disk')
                    ->label('Disk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->dateTime()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('size')
                    ->label('Size')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('disk')
                    ->label('Disk')
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (BackupDestination $record) => Storage::disk($record->disk)->download($record->path)),

                Tables\Actions\Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (BackupDestination $record) {
                        SpatieBackupDestination::create($record->disk, config('backup.backup.name'))
                            ->backups()
                            ->first(function (Backup $backup) use ($record) {
                                return $backup->path() === $record->path;
                            })
                            ->delete();

                        Notification::make()
                            ->title('Deleting this backup in background.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    #[Computed]
    public function interval(): string
    {
        return '10s';
    }
}
