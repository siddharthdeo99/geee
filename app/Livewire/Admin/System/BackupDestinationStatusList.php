<?php

namespace App\Livewire\Admin\System;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\FilamentSpatieLaravelBackupPlugin;
use App\Models\BackupDestinationStatus;

class BackupDestinationStatusList extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function render(): View
    {
        return view('livewire.admin.system.backup-destination-status-list');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(BackupDestinationStatus::query())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name'),
                Tables\Columns\TextColumn::make('disk')
                    ->label('Disk'),
                Tables\Columns\IconColumn::make('healthy')
                    ->label('Healthy')
                    ->boolean(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount'),
                Tables\Columns\TextColumn::make('newest')
                    ->label('Newest'),
                Tables\Columns\TextColumn::make('usedStorage')
                    ->label('Used Storage')
                    ->badge(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
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
