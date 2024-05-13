<?php

namespace App\Filament\Pages\Settings;

use App\Models\Ad;
use App\Models\Category;
use App\Models\Page;
use App\Settings\SEOSettings;
use App\Models\SettingsProperty;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Illuminate\Support\HtmlString;
use Spatie\Sitemap\Sitemap;

class ManageSEOSettings extends SettingsPage
{
    protected static ?string $title = 'SEO Settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'SEO';

    protected static ?string $slug = 'manage-seo-settings';

    protected static string $settings = SEOSettings::class;

    protected static ?int $navigationSort = 9;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(SEOSettings::class);
        $filtered = [];

        foreach ($data as $key => $item) {
            // Check if the property exists in the GeneralSettings class
            if (property_exists($previousData, $key)) {
                // Get the type of the property
                $propertyType = gettype($previousData->$key);

                // If the item is null and the property type is string, set it to an empty string
                if (is_null($item) && $propertyType === 'string') {
                    $filtered[$key] = '';
                    continue;
                }
            }
            // For other cases, just copy the item as is
            $filtered[$key] = $item;
        }
        return $filtered;
    }

    public function generateSitemap() {
        try {
            $ads = Ad::all();
            $categories = Category::all();
            $pages = Page::all();

            Sitemap::create()
                ->add($ads)
                ->add($categories)
                ->add($pages)
                ->writeToFile(public_path('sitemap.xml'));

            // Send a success notification
            Notification::make()
                ->title(__('messages.t_common_success'))
                ->success()
                ->send();
        } catch (\Exception $e) {
            // Send a failure notification
            Notification::make()
                ->title(__('messages.t_common_error'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('meta_title')
                ->label('Meta Title')
                ->placeholder('Enter the meta title for your site')
                ->required()
                ->helperText('This title will appear in search engine results and browser tabs.'),

                TextInput::make('meta_description')
                    ->label('Meta Description')
                    ->placeholder('Enter the meta description for your site')
                    ->required()
                    ->columnSpanFull()
                    ->helperText('This description will appear in search engine results. Limit to 155-160 characters.'),

                SpatieMediaLibraryFileUpload::make('ogimage')
                    ->label('Upload OG Image')
                    ->collection('seo')
                    ->visibility('public')
                    ->image()
                    ->model(
                        SettingsProperty::getInstance('seo.ogimage'),
                    )
                    ->helperText('Upload the Open Graph (OG) image for your website. This image will be shown when your site is shared on platforms like Facebook.'),

                TextInput::make('twitter_username')
                    ->label('Twitter Username')
                    ->placeholder('Enter your Twitter username (without @)')
                    ->helperText('Specify your Twitter username for better SEO integration with Twitter.'),

                TextInput::make('facebook_page_id')
                    ->label('Facebook Page ID')
                    ->placeholder('Enter your Facebook Page ID')
                    ->helperText('Specify your Facebook Page ID for better SEO integration with Facebook.'),

                TextInput::make('facebook_app_id')
                    ->label('Facebook App ID')
                    ->placeholder('Enter your Facebook App ID')
                    ->helperText('Provide your Facebook App ID if you have one. This helps in tracking and analytics on Facebook.'),

                Toggle::make('enable_sitemap')
                    ->label('Enable Sitemap Generation')
                    ->hint(new HtmlString('<div>
                        <a class="cursor-pointer text-blue-600 hover:underline" href="/sitemap.xml" target="_blank">View Sitemap</a> |
                        <span class="cursor-pointer text-blue-600 hover:underline" wire:click="generateSitemap">Generate Sitemap</span>
                    </div>'))
                    ->helperText('Turn this on to automatically generate a sitemap for your website. Sitemaps improve search engine indexing.')
                    ->columnspanfull(),

            ])
            ->columns(2);
    }
}
