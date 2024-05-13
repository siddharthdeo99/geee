<?php

namespace App\Filament\Pages\System;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use App\Enums\Option;
use App\Jobs\CreateBackupJob;

class Backups extends Page
{

    protected static string $view = 'filament.pages.system.backups';

    protected static ?string $navigationGroup = 'System Manager';

    protected static ?string $navigationLabel = 'Backup';

    protected ?string $heading = 'Backup';

    protected function getActions(): array
    {
        return [
            Action::make('Create Backup')
                ->button()
                ->label('Create Backup')
                ->action('openOptionModal'),
        ];
    }

    public function openOptionModal(): void
    {
        $this->dispatch('open-modal', id: 'backup-option');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function create(string $option = ''): void
    {


        CreateBackupJob::dispatch(Option::from($option))
            ->afterResponse();

        $this->dispatch('close-modal', id: 'backup-option');

        Notification::make()
            ->title('Creating a new backup in background.')
            ->success()
            ->send();
    }

}
