<?php

namespace App\Livewire\Ad\PostAd;

use App\Models\Ad;
use App\Models\AdImage;
use App\Settings\AdSettings;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\HtmlString;
use Closure;
use Filament\Notifications\Notification;

class VisualizeAd extends Component implements HasForms
{
    use InteractsWithForms;

    // Properties related to Ad and its images
    #[Reactive]
    public $id;
    public Ad $record;

    public ?array $data = [];

    /**
     * Mount the component and fetch the images associated with the ad.
     */
    public function mount() {
        $this->record = Ad::find($this->id);
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Form $form): Form
    {
        $adSettings = app(AdSettings::class);
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('ads')
                    ->label(__('messages.t_upload_photos'))
                    ->hiddenLabel()
                    ->multiple()
                    ->hint(new HtmlString('
                        <div class="flex flex-col sm:flex-row  gap-x-2 justify-between items-end sm:items-center mb-1">
                            <span class="text-gray-950  dark:text-white font-medium">' . __("messages.t_upload_hint") . '</span>
                            <div><button wire:click="uploadPhotos" class="hover:underline whitespace-nowrap px-3 py-2 bg-primary-600 text-black rounded-md mb-1 font-semibold" type="button">' . __('messages.t_save_order') . '</button></div>
                        </div>
                    '))
                    ->collection('ads')
                    ->required()
                    ->minFiles(1)
                    ->maxFiles($adSettings->image_limit)
                    ->rules([
                        function () {
                            return function (string $attribute, $value, Closure $fail) {
                                $originalName = $value->getClientOriginalName();
                                $maxLength = 191;
                                if (!mb_detect_encoding($originalName)) {
                                    $fail("The file name is too long. Maximum length allowed is {$maxLength} characters.");
                                    Notification::make()
                                    ->title("The file name is too long. Maximum length allowed is {$maxLength} characters.")
                                    ->danger()
                                    ->send();
                                }
                            };
                        },
                    ])
                    ->openable()
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->reorderable()
                    ->helperText(__('messages.t_add_photos_to_ad', ['image_limit' => $adSettings->image_limit]))
                    ->appendFiles(),
                TextInput::make('video_link')
                    ->label('YouTube Video Link (Optional)')
                    ->url()
                    ->live(onBlur: true)
                    ->suffixIcon('heroicon-m-video-camera')
                    ->placeholder('Example: http://www.youtube.com/watch?v=<your video id>')
                    ->hint('Add a YouTube video to your ad.'),

            ])
            ->statePath('data')
            ->model($this->record);
    }

    /**
     * Proceed to the next step after verifying there's at least one image.
     */
    #[On('next-clicked')]
    public function next() {
        $this->validate();
        $this->uploadPhotos(false);
        $this->dispatch('next-step');
    }

    public function uploadPhotos($showNotification = true): void
    {
        try {
            $data = $this->form->getState();
            $this->record->update($data);

            // Send a success notification only if $showNotification is true
            if ($showNotification) {
                Notification::make()
                    ->title(__('messages.t_common_success'))
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
            ->title($e->getMessage())
            ->danger()
            ->send();
        }
    }

    public function updated($name): void
    {
        if($name == 'data.video_link') {
           $this->uploadPhotos();
        }
    }

    /**
     * Render the component view.
     */
    public function render() {
        return view('livewire.ad.post-ad.visualize-ad');
    }
}
