<?php

namespace App\Livewire\Ad\PostAd;

use App\Models\Ad;
use App\Models\PriceType;
use App\Models\Category;
use App\Models\AdCondition;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Components\TagsInput;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;


class AdDetail extends Component implements HasForms
{
    use InteractsWithForms;

    public $id;
    public $showMainCategories = true;
    public $categories = [];
    #[Rule('required', message: 'messages.t_select_main_category_error')]
    public $parent_category = null;
    public $title = '';
    #[Rule('required', message: 'messages.t_select_ad_category_error')]
    public $category_id = '';
    public $description = '';
    public $price;
    public $price_type_id;
    public $condition_id;
    public $display_phone;
    public $phone_number;
    public $type;
    public $for_sale_by;
    public $tags = [];
    public Category $category;

    /**
     * Mount the component.
     *
     * @param mixed $id
     */
    public function mount($id)
    {
        $this->id = $id;

        $this->loadCategories();

        if ($this->id) {
            $this->loadAdDetails($this->id);
        }
    }

    /**
     * Load main categories and their subcategories.
     */
    private function loadCategories()
    {
        $this->categories = Category::with('subcategories')->whereNull('parent_id')->get();
    }

    /**
     * Load and set ad details if an ID is provided.
     *
     * @param int $id The ID of the ad to load
     */
    private function loadAdDetails($id)
    {
        $ad = Ad::find($id);
        if ($ad) {
            $this->title = $ad->title;
            $this->description = $ad->description;
            $this->price = $ad->price;
            $this->type = $ad->type;
            $this->for_sale_by = $ad->for_sale_by;
            $this->tags = $ad->tags ? json_decode($ad->tags) : [];
            $this->price_type_id = $ad->price_type_id;
            $this->display_phone = $ad->display_phone;
            $this->phone_number = $ad->phone_number;
            $this->condition_id = $ad->condition_id;

            if ($ad->category_id) {
                $this->category_id = $ad->category_id;
                $this->parent_category = $ad->category->parent_id;
                $this->showMainCategories = false;
            }
        }
    }

    /**
     * Define the form for the title input.
     *
     * @param Form $form
     * @return Form
     */
    public function titleInput(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label(__('messages.t_title'))
                    ->live(onBlur: true)
                    ->placeholder(__('messages.t_what_are_you_selling'))
                    ->minLength(10)
                    ->required(),
            ]);
    }

    /**
     * Define the form for the ad details.
     *
     * @param Form $form
     * @return Form
     */
    public function detailForm(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('for_sale_by')
                ->label(__('messages.t_for_sale_by'))
                ->live()
                ->options([
                    'owner' => __('messages.t_owner_for_sale'),
                    'business' => __('messages.t_business_for_sale'),
                ]),
                MarkdownEditor::make('description')
                    ->label(__('messages.t_description'))
                    ->live(onBlur: true)
                    ->minLength(20)
                    ->required(),
                Select::make('condition_id')
                    ->label(__('messages.t_condition'))
                    ->live()
                    ->options(AdCondition::all()->pluck('name', 'id')),
                Select::make('price_type_id')
                    ->label(__('messages.t_price'))
                    ->live()
                    ->required()
                    ->options(PriceType::all()->pluck('name', 'id')),
                TextInput::make('price')
                    ->helperText(__('messages.t_set_fair_price'))
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->placeholder(__('messages.t_price_your_ad'))
                    ->prefix(config('app.currency_symbol'))
                    ->live(onBlur: true)
                    ->hidden(fn (Get $get): bool => $get('price_type_id') != 1)
                    ->hiddenLabel(),
                Radio::make('display_phone')
                    ->label(__('messages.t_display_phone_number'))
                    ->live()
                    ->required()
                    ->boolean(),
                PhoneInput::make('phone_number')
                    ->placeholder(__('messages.t_enter_phone_number'))
                    ->helperText(__('messages.t_phone_number_helper'))
                    ->required()
                    ->hidden(fn (Get $get): bool => !$get('display_phone'))
                    ->hiddenLabel(),
                TagsInput::make('tags')
                    ->label(__('messages.t_tags'))
                    ->helperText(__('messages.t_set_tags'))
                    ->live(onBlur: true),
            ]);
    }

    /**
     * Get all form definitions.
     *
     * @return array
     */
    protected function getForms(): array
    {
        return [
            'titleInput',
            'detailForm',
        ];
    }

    /**
     * Handle category selection.
     *
     * @param int $categoryId
     */
    public function selectCategory($categoryId)
    {
        $this->parent_category = $categoryId;
        $this->showMainCategories = false;
    }

    /**
     * Update the slug of an ad based on its title.
     *
     * This method generates a new slug by combining a sanitized,
     * truncated version of the ad's title and its unique identifier (UUID).
     * It then saves the new slug to the ad.
     *
     * @param Ad $ad The ad instance whose slug needs to be updated.
     * @param string $title The new title of the ad.
     */
    public function updateAdSlug(Ad $ad, $title)
    {
        // Generate the slug using the title and the UUID of the ad
        $ad->slug = Str::slug(Str::limit($title, 138)) . '-' . substr($ad->id, 0, 8);;
        $ad->save();
    }


    /**
     * Handle updates to component properties.
     *
     * @param string $name
     * @param mixed $value
     */
    public function updated($name, $value)
    {
        $userId = auth()->id();
        $this->validateOnly($name);

        if (!$this->id) {
            if (!$userId) {
                abort(403, 'Unauthorized action.');
            }
            $ad = Ad::create([$name => $value, 'user_id' => $userId]);
            $this->id = $ad->id;
            $this->updateAdSlug($ad, $value);
            $this->loadAdDetails($this->id);
        } else {
            $ad = Ad::find($this->id);

            if (!$ad || $ad->user_id != $userId) {
                abort(403, 'Unauthorized action.');
            }
           // Update the "tags" property if it's an array element update
            if (str_starts_with($name, 'tags.')) {
                $index = explode('.', $name)[1];
                $tags = $this->tags;
                $tags[$index] = $value;
                $this->tags = $tags;
                $ad->update(['tags' => json_encode($tags)]);

            } else {
                // Update other fields
                $adData = [$name => $value];
                $ad->update($adData);
                if ($name == 'title') {
                    // If the title is updated, update the slug too
                    $this->updateAdSlug($ad, $value);
                }
            }
        }

        $this->dispatch('ad-created', id: $ad->id);
    }

    /**
     * Handle the next button click.
     */
    #[On('next-clicked')]
    public function next()
    {
        $this->validate();

        if (!$this->parent_category) {
            $this->addError('parent_category', __('messages.t_select_main_category'));
            return;
        }

        $this->dispatch('next-step');
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.ad.post-ad.ad-detail');
    }
}
