<?php

namespace App\Livewire\User;

use App\Forms\Components\WebCamJs;
use App\Models\VerificationCenter;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Forms\Get;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Request;

class Verification extends Component implements HasForms
{
    use SEOToolsTrait;
    use InteractsWithForms;

    public ?array $data = [];

    public VerificationCenter $record;

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';

    /**
     * Initializes the component.
     */
    public function mount()
    {
        $this->setSeoData();
        $this->populateVerificationData();
    }

    /**
     * Populate verification data into the form for the authenticated user.
     */
    protected function populateVerificationData()
    {
        // Ensure a user is authenticated
        if (!Auth::check()) {
            // Optionally handle the case where no user is logged in
            return;
        }

        $userId = Auth::id();

        // Fetch the authenticated user's verification data
        $verification = VerificationCenter::where('user_id', $userId)->first();

        if ($verification) {
            $this->record = $verification;
            $this->form->fill($this->record->attributesToArray());
        }

    }

    /**
     * Handle the form submission and create a verification record.
     */
    public function create()
    {
        $this->validate();

        $userId = Auth::id();

        // Create a new VerificationCenter record with the current user's ID
        $verification = new VerificationCenter();
        $verification->fill($this->form->getState());
        $verification->user_id = $userId;
        $verification->save();

        $this->form->model($verification)->saveRelationships();

        $this->addImageToMediaCollection($this->data['selfie'], $verification);

        $this->record = $verification;

        // Send success notification
        Notification::make()
            ->title(__('messages.t_verification_submitted'))
            ->success()
            ->send();

        $this->js('location.reload();');

    }

    #[On('take-selfie')]
    public function setSelfie($dataUri) {
        $this->data['selfie'] = $dataUri;
    }
    public function addImageToMediaCollection($dataUri, $model)
    {
        // Extract base64 content from the data URI
        $exploded = explode(',', $dataUri);
        if(count($exploded) != 2) {
            return;
        }
        $base64Image = $exploded[1];
        // Use Spatie's addMediaFromBase64 method to handle the base64 string
        $model->addMediaFromBase64($base64Image)->toMediaCollection('selfie');
    }


    /**
     * Defines the form schema for verification.
     */
    public function form(Form $form): Form
    {
        $isRecordPresent = isset($this->record);

        $isDeclined = $isRecordPresent && $this->record->status == 'declined';


        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('doc_type')
                        ->label('Choose Document Type')
                        ->schema([
                            Radio::make('document_type')
                                ->default('id')
                                ->label(__('messages.t_select_document_type') )
                                ->required()
                                ->options([
                                    'id' => __('messages.t_government_issued_id'),
                                    'driving' => __('messages.t_driver_license'),
                                    'passport' => __('messages.t_passport')
                                ])
                                ->hidden($isRecordPresent && !$isDeclined),
                        ]),
                    Wizard\Step::make('doc_image')
                        ->label('Upload Document Images')
                        ->schema([
                            Grid::make()
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('front_side')
                                    ->label($isRecordPresent ? __('messages.t_display_document_front_side') : __('messages.t_upload_your_document_front_side'))
                                    ->collection('front_side_verification')
                                    ->visibility('private')
                                    ->image()
                                    ->required()
                                    ->downloadable(),
                                SpatieMediaLibraryFileUpload::make('back_side')
                                    ->label($isRecordPresent ? __('messages.t_display_document_back_side') : __('messages.t_upload_your_document_back_side'))
                                    ->collection('back_side_verification')
                                    ->visibility('private')
                                    ->image()
                                    ->downloadable(),
                            ]),
                        ]),
                    Wizard\Step::make('selfie')
                        ->label('Capture Selfie with Webcam')
                        ->schema([
                            WebCamJs::make('selfie')
                            ->label(__('messages.t_take_a_selfie'))
                            ->helperText(__('messages.t_ensure_clear_visibility'))
                            ->required(),
                        ]),
                ])
                ->hidden(fn (Get $get): bool => $isRecordPresent && !$isDeclined)
                ->submitAction(new HtmlString(Blade::render(<<<BLADE
                        <x-filament::button
                            type="submit"
                        >
                          Submit & Verify
                        </x-filament::button>
                BLADE))),
                Grid::make()
                ->schema([
                    Placeholder::make('docs_status')
                    ->label(__('messages.t_documentation_status'))
                    ->content(function () {
                        $status = $this->record->status ?? 'not submitted';
                        $dateLabel = '';

                        switch ($status) {
                            case 'verified':
                                $date = $this->record->verified_at ? $this->record->verified_at : __('messages.t_no_date_available');
                                $dateLabel = __('messages.t_verified_on', ['date' => $date]);
                                break;
                            case 'declined':
                                $date = $this->record->declined_at ? $this->record->declined_at : __('messages.t_no_date_available');
                                $dateLabel = __('messages.t_declined_on', ['date' => $date]);
                                break;
                            default:
                                $date = $this->record->created_at ? $this->record->created_at : __('messages.t_no_date_available');
                                $dateLabel = __('messages.t_submitted_on', ['date' => $date]);
                                break;
                        }

                        return new HtmlString(ucfirst($status) . ' - ' . $dateLabel);
                    }),

                    Placeholder::make('submitted_document_type')
                        ->label(__('messages.t_submitted_document_type'))
                        ->content(function () {
                            $type = $this->record->document_type ?? 'not submitted';

                            switch ($type) {
                                case 'id':
                                    $typeLabel = __('messages.t_government_issued_id');
                                    break;
                                case 'driving':
                                    $typeLabel = __('messages.t_driver_license');
                                    break;
                                case 'passport':
                                    $typeLabel = __('messages.t_passport');
                                    break;
                                default:
                                    $typeLabel = ucfirst($type);
                                    break;
                            }

                            return new HtmlString($typeLabel);
                        }),



                    Grid::make()
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('front_side')
                                ->label($isRecordPresent ? __('messages.t_display_document_front_side') : __('messages.t_upload_your_document_front_side'))
                                ->collection('front_side_verification')
                                ->visibility('private')
                                ->hidden(fn (Get $get): bool => empty($get('front_side')))
                                ->image()
                                ->disabled($isRecordPresent && !$isDeclined),
                            SpatieMediaLibraryFileUpload::make('back_side')
                                ->label($isRecordPresent ? __('messages.t_display_document_back_side') : __('messages.t_upload_your_document_back_side'))
                                ->collection('back_side_verification')
                                ->visibility('private')
                                ->image()
                                ->hidden(fn (Get $get): bool => empty($get('back_side')))
                                ->disabled($isRecordPresent && !$isDeclined),
                            SpatieMediaLibraryFileUpload::make('selfie')
                                ->label(__('messages.t_selfie'))
                                ->collection('selfie')
                                ->visibility('private')
                                ->downloadable()
                                ->image()
                                ->disabled($isRecordPresent && !$isDeclined)
                        ]),

                    Placeholder::make('comments')
                        ->label(__('messages.t_documentation_comments'))
                        ->content(function () {
                            $comments = $this->record->comments;
                            return new HtmlString($comments ? ucfirst($comments) : '-');
                        })
                        ->visible($isRecordPresent),
                    ])
                    ->columns(1)
                    ->hidden(fn (): bool => !$isRecordPresent || $isDeclined )
            ])
            ->statePath('data')
            ->model($isRecordPresent ? $this->record : VerificationCenter::class);
    }


    /**
    * Set SEO data
    */
    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings = app(SEOSettings::class);


        $separator = $generalSettings->separator ?? '-';
        $siteName = $generalSettings->site_name ?? 'AdFox';

        $title = __('messages.t_seo_verification_page_title') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    public function updateDocs()
    {
        $data = $this->form->getState();

        $this->record->update(array_merge($data, ['status' => 'pending']));

        Notification::make()
        ->title(__('messages.t_documents_pending_review'))
        ->success()
        ->send();

        $this->js('location.reload();');
    }

    public function getCurrentAction()
    {
        return isset($this->record) && $this->record->status == 'declined' ? 'updateDocs' : 'create';
    }

    /**
     * Renders the MyAccount view.
     */
    public function render()
    {
        return view('livewire.user.verification');
    }
}
