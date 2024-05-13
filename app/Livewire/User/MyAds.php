<?php

namespace App\Livewire\User;

use App\Models\Ad;
use App\Models\AdPromotion;
use App\Models\OrderPackage;
use App\Models\UserAdPosting;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Livewire\Attributes\Url;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Settings\GeneralSettings;
use App\Settings\PackageSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Filament\Tables\Actions\ActionGroup;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;

/**
 * MyAds Component.
 * Allows users to view and manage their ads with actions like preview, edit, and delete.
 */
class MyAds extends Component implements HasForms, HasTable
{

    use InteractsWithTable, InteractsWithForms, SEOToolsTrait;

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';

    /**
     * Mount lifecycle hook.
     */
    public function mount()
    {
        $this->setSeoData();
    }

    /**
     * Defines the table structure for displaying ads.
     */

    public function table(Table $table): Table
    {

        $userId = auth()->id();
        $headingDescription = false;

        // Check if 'packages' plugin is active and package settings are enabled
        if (app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status) {
            $userAdPosting = UserAdPosting::where('user_id', $userId)->first();
            $freeAdLimitUsed = $userAdPosting ? $userAdPosting->free_ad_count : 0;
            $freeAdLimit = app(PackageSettings::class)->free_ad_limit;
            $availableAdLimit = $freeAdLimit - $freeAdLimitUsed;
            $renewalPeriod = app(PackageSettings::class)->ad_renewal_period;

            $headingDescription = __('messages.t_free_ads_used') . ": {$freeAdLimitUsed}, " .
            __('messages.t_available_free_ads') . ": {$availableAdLimit}, " .
            __('messages.t_renewal_period') . ": " . ucfirst($renewalPeriod);

        }
        return $table
            ->query(Ad::where('user_id', auth()->id()))
            ->modifyQueryUsing(fn (Builder $query) => $query->latest())
            ->description($headingDescription)
            ->columns([
                SpatieMediaLibraryImageColumn::make('ads')
                ->collection('ads')
                ->conversion('thumb')
                ->defaultImageUrl(asset('images/placeholder.jpg'))
                ->label(__('messages.t_ad_images'))
                ->size(40)
                ->circular()
                ->overlap(2)
                ->stacked()
                ->limit(3),
                TextColumn::make('title')
                ->label(__('messages.t_ad_title'))
                ->searchable(),
                TextColumn::make('posted_date')->label(__('messages.t_posted_on_date'))->date(),

                TextColumn::make('status')
                    ->label(__('messages.t_status'))
                    ->sortable(),
                SelectColumn::make('status')
                ->selectablePlaceholder(false)
                ->options([
                    'draft' => 'Draft',
                    'active' => 'Active',
                    'sold' => 'Sold',
                    'inactive' => 'Deactivated',
                    'expired' => 'Expired',
                    'pending' => 'Pending'
                ])->label(__('messages.t_change_status_action'))
                ->disableOptionWhen(fn (string $value): bool => $value === 'inactive' || $value === 'expired' || $value === 'pending' )
                ->disabled(function ($state) {
                  return $state->value === 'expired' || $state->value === 'inactive' || $state->value === 'pending' ;
                })
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'sold' => 'Sold',
                    'expired' => 'Expired'
                ]),
            ])
            ->actions([
                Action::make('view')
                ->icon('heroicon-o-eye')
                ->label(__('messages.t_preview_ad'))
                ->url(fn (Ad $record): string =>  route('ad-details', [

                    'slug' => $record->slug
                ]))
                ->openUrlInNewTab(),

                Action::make('sell')
                ->icon('heroicon-o-bolt')
                ->button()
                ->color('success')
                ->action(fn (Ad $record) => $this->redirectToPackageSelection($record))
                ->visible(fn (Ad $record) => $record->status->value == 'active')
                ->label(__('messages.t_boost_your_sale')),

                Action::make('view_status')
                ->label(__('messages.t_view_status_ad'))
                ->modalSubmitAction(false)
                ->color('info')
                ->modalHeading(fn (Ad $record) => __('messages.t_ad_status'))
                ->modalDescription(fn (Ad $record) => new HtmlString(self::generateStatusDescription($record))),

                ActionGroup::make([
                    EditAction::make()
                    ->url(fn (Ad $record): string => route('post-ad', ['id' => $record->id])),

                    DeleteAction::make(),

                ])
                ->icon('heroicon-m-ellipsis-horizontal')
                ->tooltip('Actions'),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function generateStatusDescription(Ad $record)
    {
        $activePromotions = AdPromotion::where('ad_id', $record->id)
                                        ->whereDate('end_date', '>=', now())
                                        ->get();

        $status = ucfirst($record->status->value);
        $statusDescription = "<p>" . __('messages.t_ad_current_status', ['status' => $status]) . "</p>";

        if ($activePromotions->isNotEmpty()) {
            $statusDescription .= "<p>" . __('messages.t_with_active_promotions') . "</p>";
            $statusDescription .= "<ul>";

            foreach ($activePromotions as $promotion) {
                $statusDescription .= "<li>" . __('messages.t_promotion_active_until', [
                    'promotionName' => $promotion->promotion->name,
                    'date' => $promotion->end_date->format('M d, Y')
                ]) . "</li>";
            }

            $statusDescription .= "</ul>";
        } else {
            $statusDescription .= "<p>" . __('messages.t_no_additional_promotions') . "</p>";
        }

        return $statusDescription;
    }

    public function redirectToPackageSelection(Ad $record)
    {
        if(app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status) {
            $userOrderPackages = OrderPackage::where('user_id', auth()->id())
                                            ->whereHas('packageItems', function ($query) {
                                                $query->whereDate('expiry_date', '>=', now())
                                                    ->where('type', 'promotion')
                                                    ->where('available', '>', 0);
                                            })
                                            ->first();

            $actionType = $userOrderPackages ? 'apply' : 'single';

            $routeParameters = [
                'pkg_type' => $actionType,
                'ad_id' => $record->id,
            ];

            return redirect()->route('choose-package', $routeParameters);
        } else {
            $routeParameters = [
                'id' => $record->id,
                'current' => 'ad.post-ad.promote-ad',
            ];
            return redirect()->route('post-ad', $routeParameters);
        }
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

        $title = __('messages.t_seo_post_ad_page_title') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    /**
     * Renders the MyAds view.
     */
    public function render()
    {
        return view('livewire.user.my-ads');
    }
}
