<?php

namespace App\Filament\Pages\System;

use App\Services\LogViewer;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Logs extends Page
{

    protected static ?string $navigationGroup = 'System Manager';

    protected static ?string $navigationLabel = 'Logs';

    protected ?string $heading = 'Logs';

    protected static string $view = 'filament.pages.system.logs';

    public ?string $logFile = null;

    /**
     * @throws FileNotFoundException
     */
    public function getLogs(): Collection
    {
        if (!$this->logFile) {
            return collect([]);
        }

        $logs = LogViewer::getAllForFile($this->logFile);

        return collect($logs);
    }

    /**
     * @throws Exception
     */
    public function download(): BinaryFileResponse
    {
        return response()->download(LogViewer::pathToLogFile($this->logFile));
    }

    /**
     * @throws Exception
     */
    public function delete(): bool
    {
        if (File::delete(LogViewer::pathToLogFile($this->logFile))) {
            $this->logFile = null;

            return true;
        }

        abort(404, __('No such file'));
    }

    protected function getForms(): array
    {
        return [
            'search' => $this->makeForm()
                ->schema($this->getFormSchema()),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('logFile')
                ->searchable()
                ->reactive()
                ->hiddenLabel()
                ->placeholder('Select or search a log file')
                ->options(fn () => $this->getFileNames($this->getFinder())->take(5))
                ->getSearchResultsUsing(fn (string $query) => $this->getFileNames($this->getFinder()->name("*{$query}*"))),
        ];
    }

    protected function getFileNames($files): Collection
    {
        return collect($files)->mapWithKeys(function (SplFileInfo $file) {
            return [$file->getRealPath() => $file->getRealPath()];
        });
    }


    protected function getFinder(): Finder
    {
        return Finder::create()
            ->ignoreDotFiles(true)
            ->ignoreUnreadableDirs()
            ->files()
            ->in(storage_path('logs'));
    }

}
