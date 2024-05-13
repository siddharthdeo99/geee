<?php

namespace App\Livewire\User;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Livewire\Component;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

/**
 * MyProfile Component.
 * Allows users to update and manage their profile information.
 */
class MyProfile extends Component implements HasForms
{
    public User $user;
    use InteractsWithForms;
    use SEOToolsTrait;

    public ?array $data = [];

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';

    /**
     * Mount lifecycle hook.
     * Fetches the user's data and populates the form.
     */
    public function mount(User $user): void
    {
        $this->populateUserData();
        $this->setSeoData();
    }

    /**
     * Populate user data into the form.
     */
    protected function populateUserData()
    {
        // Fetch the authenticated user's data
        $user = Auth::user();

        $this->user = $user;

        $this->form->fill($this->user->attributesToArray());
    }

    /**
     * Handle the form submission and update the user's profile.
     */
    public function create()
    {
        $user = Auth::user();
        $user->fill($this->form->getState());
        $user->save();

        Notification::make()
            ->title(__('messages.t_profile_updated'))
            ->success()
            ->send();
    }

    /**
     * Defines the form schema for updating the user's profile.
     */
    public function form(Form $form): Form
    {
        $userId = auth()->id();
        $path = "users/user-{$userId}/profile";

        $fileUpload = $this->configureProfileImageUpload($path);

        return $form
            ->schema($this->buildFormSchema($fileUpload))
            ->statePath('data')
            ->model($this->user);
    }

    /**
     * Configures the profile image upload component.
     */
    protected function configureProfileImageUpload(string $path): FileUpload
    {

        $fileUpload =  SpatieMediaLibraryFileUpload::make('profile_image')
            ->label(__('messages.t_profile_image'))
            ->collection('profile_images')
            ->visibility('private')
            ->image()
            ->imageEditor();

        $storageType = config('filesystems.default');
        if ($storageType == 's3') {
            $fileUpload->disk($storageType);
        }

        return $fileUpload;
    }

    /**
     * Builds the form schema for user profile update.
     */
    protected function buildFormSchema(FileUpload $fileUpload): array
    {
        return [
            Section::make('Personal Information')
                ->description(__('messages.t_update_name_bio_image'))
                ->schema($this->personalInformationSchema($fileUpload))
                ->columns(2),

            Section::make(__('messages.t_contact_info'))
                ->description(__('messages.t_update_email_phone'))
                ->schema($this->contactInformationSchema())
                ->columns(2),
        ];
    }

    /**
     * Personal information schema.
     */
    protected function personalInformationSchema(FileUpload $fileUpload): array
    {
        return [
            TextInput::make('name')
                ->label(__('messages.t_name'))
                ->required()
                ->maxLength(255),
            Textarea::make('about_me')
                ->label(__('messages.t_about_me'))
                ->maxLength(500)
                ->placeholder('Tell us a bit about yourself'),
            $fileUpload
        ];
    }

    /**
     * Contact information schema.
     */
    protected function contactInformationSchema(): array
    {
        return [
            TextInput::make('email')
                ->label(__('messages.t_email'))
                ->required()
                ->email()
                ->maxLength(255),
            PhoneInput::make('phone_number')
                ->label(__('messages.t_phone_number'))
                ->placeholder(__('messages.t_enter_phone_number')),
        ];
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

        $title = __('messages.t_seo_my_profile_page_title') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    /**
     * Renders the MyProfile view.
     */
    public function render()
    {
        return view('livewire.user.my-profile');
    }
}
