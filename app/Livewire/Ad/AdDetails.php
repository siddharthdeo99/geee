<?php

namespace App\Livewire\Ad;

use App\Models\Ad;
use App\Models\AdFieldValue;
use App\Models\AdPromotion;
use Livewire\Component;
use App\Models\Conversation;
use App\Models\FavouriteAd;
use App\Models\Message;
use App\Models\Promotion;
use App\Models\ReportedAd;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Actions\Action;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Parsedown;

class AdDetails extends Component implements HasForms
{
    use SEOToolsTrait;
    use InteractsWithForms;
    // Properties
    public $ad;
    public $isFavourited = false;
    public $isFeatured = false;
    public $isUrgent = false;
    public $isWebsite = false;
    public $metaTitle;
    public $metaDescription;
    public $fieldDetails;
    public $descriptionHtml;
    #[Url(as: 'admin_view')]
    public $ownerView;
    public $referrer = false;

    public $breadcrumbs = [];
    public $relatedAds = [];

    public $data = [];

    public $tags;

    /**
     * Mount the component with the provided ad details.
     *
     * @param Ad $ad The ad to display.
     */
    public function mount($slug)
    {
        $this->initializeAd($slug);
        $this->checkPromotions();
        $this->setSEOData();
        $this->buildBreadcrumbs();
        $this->fetchRelatedAds();
        $this->fetchTags();
    }

    /**
     * Initialize the ad details and handle potential access issues.
     *
     * @param Ad $ad The ad to display.
     */
    protected function initializeAd($slug)
    {
        $this->ad = Ad::where('slug', $slug)->first();
        if (!$this->ad) {
            abort(404, 'Ad not found');
        }
        $this->checkAdAccess();
        $this->setDescriptionHtml();
        $this->fetchFieldDetails();
        $this->checkFavouriteStatus();
    }

    protected function fetchTags()
    {
        if (!empty($this->ad->tags)) {
            $this->tags = collect(json_decode($this->ad->tags, true))
                         ->map(function ($tag) {
                             $url = url('search') . '?query[sortBy]=date&query[search]=' . urlencode($tag);
                             return [
                                 'name' => strtolower($tag),
                                 'link' => $url
                             ];
                         });
        } else {
            $this->tags = collect();
        }
    }

    /**
     * Ensure only owners or authorized individuals can see non-active ads.
     */
    protected function checkAdAccess()
    {
        $isActive = $this->ad->status->value === 'active';
        $isOwner = Auth::id() === $this->ad->user_id || $this->ownerView;
        if (!$isActive && !$isOwner) {
            abort(404, 'Ad not found or inactive');
        }
        $this->ownerView = !$isActive && $isOwner;
    }

    /**
     * Convert the ad description to HTML.
     */
    protected function setDescriptionHtml()
    {
        $parsedown = new ParseDown();
        $this->descriptionHtml = $parsedown->text($this->ad->description);
    }

    /**
     * Fetch saved field details for the ad.
     */
    protected function fetchFieldDetails()
    {
        $this->fieldDetails = AdFieldValue::where('ad_id', $this->ad->id)
                                          ->with('field')
                                          ->get()
                                          ->map(function ($adFieldValue) {
                                              return $this->transformFieldValue($adFieldValue);
                                          })
                                          ->toArray();
    }

    /**
     * Transform the field value for easier use.
     *
     * @param AdFieldValue $adFieldValue The field value to transform.
     * @return array The transformed field value.
     */
    protected function transformFieldValue($adFieldValue)
    {
        $fieldType = $adFieldValue->field->type->value;
        $value = json_decode($adFieldValue->value, true);
        if ($fieldType === 'select' || $fieldType === 'radio') {
            $options = $adFieldValue->field->options;
            $value = $options[$value] ?? $value;
        } elseif ($fieldType === 'checkbox') {
            $value = $value ? 'Yes' : 'No';
        }
        return [
            'field_name' => $adFieldValue->field->name,
            'value' => $value,
        ];
    }

    /**
     * Check the promotions associated with the ad.
     */
    protected function checkPromotions()
    {
        $currentDate = now();
        $this->isFeatured = $this->isPromotionActive(1, $currentDate);
        $this->isUrgent = $this->isPromotionActive(3, $currentDate);
        $this->isWebsite = $this->isPromotionActive(4, $currentDate);
    }

    /**
     * Check if a given promotion is active for the ad.
     *
     * @param int $promotionId The ID of the promotion to check.
     * @param string $currentDate The current date.
     * @return bool True if the promotion is active, false otherwise.
     */
    protected function isPromotionActive($promotionId, $currentDate)
    {
        return AdPromotion::where('ad_id', $this->ad->id)
                          ->where('promotion_id', $promotionId)
                          ->where('start_date', '<=', $currentDate)
                          ->where('end_date', '>=', $currentDate)
                          ->exists();
    }


    /**
     * Check if the ad is favorited by the current user.
     */
    protected function checkFavouriteStatus()
    {
        $this->isFavourited = FavouriteAd::where('user_id', Auth::id())
                                        ->where('ad_id', $this->ad->id)
                                        ->exists();
    }
    /**
     * Begin a chat with the ad seller.
     *
     * @param string|null $messageContent The initial message content.
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse A redirection response.
     */
    public function chatWithSeller($messageContent = null)
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            Notification::make()
            ->title(__('messages.t_must_be_logged_to_chat'))
            ->danger()
            ->send();
            return redirect(route('login'));
        }

        // Get the authenticated user's ID
        $buyerId = Auth::id();

        // Prevent the owner of the ad from chatting with themselves
        if ($buyerId == $this->ad->user_id) {
            Notification::make()
            ->title(__('messages.t_cannot_chat_with_yourself'))
            ->danger()
            ->send();
            return;
        }


        // Check if a conversation already exists
        $conversation = Conversation::where('ad_id', $this->ad->id)
                                    ->where('buyer_id', $buyerId)
                                    ->where('seller_id', $this->ad->user_id)
                                    ->first();

        // If conversation does not exist, create one
        if (!$conversation) {
            $conversation = Conversation::create([
                'ad_id' => $this->ad->id,
                'buyer_id' => $buyerId,
                'seller_id' => $this->ad->user_id
            ]);
        }

        // If there's a message content passed, send it
        if ($messageContent) {
            $this->sendMessage($conversation->id, $this->ad->user_id, $messageContent);
        }
        // Redirect to the messaging page with the conversation_id
        return redirect('/my-messages?conversation_id=' . $conversation->id);
    }

    /**
     * Send a message within a conversation.
     *
     * @param int $conversationId The ID of the conversation.
     * @param int $receiverId The ID of the receiver.
     * @param string $content The content of the message.
     */
    public function sendMessage($conversationId, $receiverId, $content)
    {
        Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'content' => $content
        ]);
    }

    /**
     * Add or remove the ad from the user's favorites.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse A redirection response.
     */
    public function addToFavourites()
    {
        // Check if the user is not logged in
        if (!Auth::check()) {
            // If not logged in, redirect to login page or show a message.
            Notification::make()
            ->title(__('messages.t_login_to_add_favorites'))
            ->success()
            ->send();
            return redirect(route('login'));
        }

        // Check if already added to favorites
        $favourite = FavouriteAd::where('user_id', Auth::id())
                                 ->where('ad_id', $this->ad->id)
                                 ->first();

        if ($favourite) {
            $favourite->delete();
            $this->isFavourited = false;
        } else {
            FavouriteAd::create([
                'user_id' => Auth::id(),
                'ad_id' => $this->ad->id,
            ]);
            $this->isFavourited = true;
        }
    }

    /**
     * Builds the breadcrumb trail based on the ad's category and subcategory.
     */
    protected function buildBreadcrumbs()
    {
        // Start with the home breadcrumb
        $this->breadcrumbs['/'] = 'Home';

        $category = null;
        $subCategory = null;

        // If the ad has a subcategory, add it to the breadcrumbs
        if ($this->ad->category && $this->ad->category->parent) {
            $subCategory = $this->ad->category;
            $category = $this->ad->category->parent;
            $this->breadcrumbs['/categories/' . $category->slug] = $category->name;
            $this->breadcrumbs['/categories/' . $category->slug . '/' . $subCategory->slug] = $subCategory->name;
        }
    }


    /**
     * Define the form for reporting an advertisement.
     *
     * This form allows users to report an ad by providing a reason,
     * ensuring that ads maintain the platform's standards and guidelines.
     *
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('reason')
                    ->label(__('messages.t_report_reason_label'))
                    ->placeholder(__('messages.t_report_reason_placeholder'))
                    ->required()
                    ->maxLength(500)
                    ->helperText(__('messages.t_report_reason_helper'))
            ])
            ->statePath('data');
    }

    /**
     * Perform common checks before reporting an ad.
     *
     * @return bool Indicates if the process should continue.
     */
    private function performReportAdChecks() {
        if (auth()->guest()) {
            Notification::make()
                ->title(__('messages.t_login_or_signup_to_report_ad'))
                ->info()
                ->send();
            return false;
        }

        if ($this->ad->user_id == auth()->id()) {
            Notification::make()
                ->title(__('messages.t_cannot_report_own_ad'))
                ->danger()
                ->send();
            return false;
        }

        return true;
    }


    /**
     * Open the report ad modal if the user is authenticated and not reporting their own ad.
     *
     * This function checks if the user is logged in. If not, it redirects them to the login page
     * with a notification encouraging them to log in or sign up to report the ad. It also ensures
     * that users cannot report their own ads by showing a notification if they attempt to do so.
     */
    public function openReportAd() {
        if (!$this->performReportAdChecks()) {
            return;
        }
        // Dispatching open modal event
        $this->dispatch('open-modal', id: 'report-ad');
    }

    /**
     * Submit a report for an advertisement.
     *
     * Creates a new record in the ReportedAd model with details about the report.
     */
    public function reportAd()
    {

        if (!$this->performReportAdChecks()) {
            return;
        }
        // Create a new reported ad record
        ReportedAd::create([
            'user_id' => Auth::id(),
            'ad_id' => $this->ad->id, // Assuming $this->ad contains the Ad model instance
            'reason' => $this->data['reason']
        ]);

         // Show success notification
        Notification::make()
        ->title(__('messages.t_report_submitted_successfully'))
        ->success()
        ->send();

         // Dispatching open modal event
         $this->dispatch('close-modal', id: 'report-ad');
    }

    /**
     * Fetches related ads based on category and tags of the current ad.
     * Excludes the current ad and limits the result set for relevance and performance.
    */
    public function fetchRelatedAds()
    {
        $currentAd = $this->ad;

        // Start the query
        $query = Ad::query();

        // Always include the category match
        $query->where('category_id', $currentAd->category_id);

        // Filter only active ads
        $query->where('status', 'active');

        // Exclude the current ad and limit the results
        $relatedAds = $query->where('id', '!=', $currentAd->id)
                            ->limit(10)
                            ->get();

        $this->relatedAds = $relatedAds;
    }


    /**
     * Render the component.
     *
     * @return \Illuminate\View\View The view to render.
     */
    public function render()
    {
        return view('livewire.ad.ad-details');
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

        $title = $this->ad->title . " $separator " . $siteName;
        $description = $this->ad->description ?? 'AdFox';
        $ogImage = $this->ad->og_image;
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
        $this->seo()->setCanonical(url()->current());
        $this->seo()->opengraph()->setTitle($title);
        $this->seo()->opengraph()->setDescription($description);
        $this->seo()->opengraph()->setUrl(url()->current());
        $this->seo()->opengraph()->setType('website');
        $this->seo()->opengraph()->addImage($ogImage);
        $this->seo()->twitter()->setImage($ogImage);
        $this->seo()->twitter()->setUrl(url()->current());
        $this->seo()->twitter()->setSite("@" . $seoSettings->twitter_username);
        $this->seo()->twitter()->addValue('card', 'summary_large_image');
        $this->seo()->metatags()->addMeta('fb:page_id', $seoSettings->facebook_page_id, 'property');
        $this->seo()->metatags()->addMeta('fb:app_id', $seoSettings->facebook_app_id, 'property');
        $this->seo()->metatags()->addMeta('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1', 'name');
        $this->seo()->jsonLd()->setTitle($title);
        $this->seo()->jsonLd()->setDescription($description);
        $this->seo()->jsonLd()->setUrl(url()->current());
        $this->seo()->jsonLd()->setType('WebSite');
    }
}
