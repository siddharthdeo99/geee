<?php

namespace App\Filament\Pages\Settings;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class ManageFileStorageSettings extends Page
{
    public ?array $data = [];

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'File Storage';

    protected static string $view = 'filament.pages.settings.manage-file-storage-settings';

    protected static ?int $navigationSort = 5;


    public function mount(): void
    {
        $this->data = [
            'storage_type' => config('filament.default_filesystem_disk'),
            's3_key' => config('filesystems.disks.s3.key'),
            's3_secret' => config('filesystems.disks.s3.secret'),
            's3_region' => config('filesystems.disks.s3.region'),
            's3_bucket' => config('filesystems.disks.s3.bucket'),
        ];
        $this->form->fill($this->data);
    }

    public function setEnvValue($values)
    {
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                setEnvironmentValue($envKey, $envValue);
            }
        }
        return true;
    }

    public function save()
    {
        try {
            $data = $this->form->getState();
            if (array_key_exists('storage_type', $data)) {
                if ($data['storage_type'] === 's3') {

                    $env_val['FILAMENT_FILESYSTEM_DISK'] =  's3';
                    $env_val['AWS_ACCESS_KEY_ID'] =  $data['s3_key'];
                    $env_val['AWS_SECRET_ACCESS_KEY'] =  $data['s3_secret'];
                    $env_val['AWS_DEFAULT_REGION'] =  $data['s3_region'];
                    $env_val['AWS_BUCKET'] =  $data['s3_bucket'];

                    $this->setEnvValue($env_val);

                } elseif ($data['storage_type'] === 'media') {
                    $env_val['FILAMENT_FILESYSTEM_DISK'] =  'media';
                    $this->setEnvValue($env_val);
                }
            }
            // Clear cache
            Artisan::call('config:clear');

            Notification::make()
                ->title('Saved.')
                ->success()
                ->send();
        } catch (\Throwable $th) {
            // Error
            Notification::make()
                ->title('Something went wrong.')
                ->danger()
                ->send();
            throw $th;
        }
    }
    public function form(Form $form): Form
    {
        $isDemo = Config::get('app.demo');

        return $form->schema([
            $isDemo ?
                Placeholder::make('storage_type')
                ->content(fn (Get $get) => $get('storage_type'))
                ->hint('Hidden due to demo mode.') :
                Select::make('storage_type')
                ->label('Storage Type')
                ->placeholder('Select Storage Type')
                ->required()
                ->live()
                ->options([
                    'media' => 'Local',
                    's3' => 'Amazon S3',
                ])
                ->hint('Select where you want to store the uploaded files.'),

            Grid::make()->schema([
                $isDemo ?
                    Placeholder::make('s3_key')
                    ->content('*****')
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint('Hidden due to demo mode.') :
                    TextInput::make('s3_key')
                    ->label('Amazon S3 Key')
                    ->placeholder('Enter Amazon S3 Key')
                    ->required()
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint('Your Amazon S3 access key.'),

                $isDemo ?
                    Placeholder::make('s3_secret')
                    ->content('*****')
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint('Hidden due to demo mode.') :
                    TextInput::make('s3_secret')
                    ->label('Amazon S3 Secret')
                    ->placeholder('Enter Amazon S3 Secret')
                    ->required()
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint('Your Amazon S3 secret access key.'),

                $isDemo ?
                    Placeholder::make('s3_region')
                    ->content('*****')
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint('Hidden due to demo mode.') :
                    TextInput::make('s3_region')
                    ->label('Amazon S3 Region')
                    ->placeholder('Enter Amazon S3 Region')
                    ->required()
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint('Your Amazon S3 storage region.'),

                $isDemo ?
                    Placeholder::make('s3_bucket')
                    ->content('*****')
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint('Hidden due to demo mode.') :
                    TextInput::make('s3_bucket')
                    ->label('Amazon S3 Bucket')
                    ->placeholder('Enter Amazon S3 Bucket')
                    ->required()
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint('Your Amazon S3 bucket name.'),
            ])
        ])
            ->columns(2)
            ->statePath('data');
    }
}
