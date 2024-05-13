<?php

namespace App\Livewire\Ad\PostAd;

use App\Models\Ad;
use App\Models\OrderPackageItem;
use App\Models\UsedPackageItem;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Settings\LocationSettings;
use App\Settings\GoogleLocationKitSettings;
use App\Settings\PackageSettings;
use App\Models\UserAdPosting;
use Carbon\Carbon;

class LocateAd extends Component
{
    // Properties related to the ad's location
    public $latitude;
    public $longitude;
    public $locationName;
    public $locationDisplayName;
    public $locationBlocked = false;
    public $postal_code;
    public $city;
    public $state;
    public $country;

    // Ad ID property
    public $id;

    /**
     * Mount the component and set the properties if an ad ID is provided.
     */
    public function mount($id)
    {
        $this->id = $id;
        if ($this->id) {
            $ad = Ad::find($this->id);
            if ($ad) {
                $this->latitude = $ad->latitude;
                $this->longitude = $ad->longitude;
                $this->locationName = $ad->location_name;
                $this->postal_code = $ad->postal_code;
                $this->city = $ad->city;
                $this->state = $ad->state;
                $this->country = $ad->country;
            }
        }
    }

    /**
     * When certain properties are updated, update the location.
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['postal_code','latitude', 'longitude', 'locationName', 'locationDisplayName', 'country',  'state',  'city'])) {
            $this->updateLocation();
        }
    }

    public function getGoogleSettingsProperty()
    {
        return app(GoogleLocationKitSettings::class);
    }

    public function getLocationSettingsProperty()
    {
        return app(LocationSettings::class);
    }

    /**
     * Update the ad's location details in the database.
     */
    public function updateLocation()
    {
        $ad = Ad::find($this->id);
        if (!$ad) {
            abort(404, 'Advertisement not found.');
        }

        $this->authorize('update', $ad);

        $ad->update([
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location_name' => $this->locationName,
            'location_display_name' => $this->locationDisplayName,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
        ]);
    }

    /**
     * Proceed to the next step after verifying the location details.
     */
    #[On('next-clicked')]
    public function next()
    {
        if (empty($this->postal_code) || empty($this->locationName) ||  empty($this->state) || empty($this->country)) {
            $this->addError('locationName', __('messages.t_location_select_prompt'));
            return;
        }
        $this->dispatch('next-step');
    }


    /**
     * Redirect to the success page after publishing.
     */
    #[On('publish-clicked')]
    public function publish()
    {
        if (empty($this->postal_code) || empty($this->locationName) ||  empty($this->state) || empty($this->country)) {
            $this->addError('locationName', __('messages.t_location_select_prompt'));
            return;
        } else {
            if(app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status) {
              // Update UserAdPosting record
              $this->updateUserAdPosting();
            }
            return redirect()->route('success-ad', ['id' => $this->id]);
        }
    }


    /**
     * Update the UserAdPosting record for the current user.
     */
    protected function updateUserAdPosting()
    {
        $userId = auth()->id();
        $userAdPosting = UserAdPosting::firstOrCreate(
            ['user_id' => $userId],
            ['period_start' => Carbon::now()]
        );

        $renewalPeriod = app(PackageSettings::class)->ad_renewal_period;
        $now = Carbon::now();

        // Determine the next period start date based on the renewal period
        $nextPeriodStart = $renewalPeriod === 'year' ?
                           $userAdPosting->period_start->addYear() :
                           $userAdPosting->period_start->addMonth();

        // Reset the ad count if the current date is greater or equal to the next period start date
        if ($now->greaterThanOrEqualTo($nextPeriodStart)) {
            $userAdPosting->ad_count = 0;
            $userAdPosting->free_ad_count = 0;
            $userAdPosting->period_start = $now;
        }

        // Increment ad count
        $userAdPosting->ad_count++;

        // Calculate the total available limit from active package items
        $activeAdLimit = OrderPackageItem::whereHas('orderPackage', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('status', 'active');
        })
        ->where('type', 'ad_count')
        ->where('expiry_date', '>=', $now)
        ->sum('available');

        // Free ad limit from settings
        $freeAdLimit = app(PackageSettings::class)->free_ad_limit;

        // Total ad limit is the sum of free limit and active ad limit
        $totalAdLimit = $freeAdLimit + $activeAdLimit;

        // Check if the user has reached the ad limit
        if ($userAdPosting->ad_count > $totalAdLimit) {
            return redirect()->route('ad-limit-reached');
        } else {
            // Check for an active ad package
            $activeAdPackage = OrderPackageItem::whereHas('orderPackage', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 'active');
            })
            ->where('type', 'ad_count')
            ->where('expiry_date', '>=', $now)
            ->where('available', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->first();

            if ($activeAdPackage) {
                // Decrement an ad from the active package
                $activeAdPackage->decrement('available');
                $activeAdPackage->increment('used');

                UsedPackageItem::create([
                    'ad_id' =>  $this->id,
                    'order_package_item_id' =>  $activeAdPackage->id,
                ]);

            } else {
                $userAdPosting->free_ad_count++;
            }
            $userAdPosting->save();
        }
    }




    /**
     * Render the component view.
     */
    public function render()
    {
      // Determine which view to render based on the presence of the 'google-location-kit' plugin
      $view = app('filament')->hasPlugin('google-location-kit') && app(GoogleLocationKitSettings::class)->status ? 'google-location-kit::locate-ad' : 'livewire.ad.post-ad.locate-ad';
      return view($view);
    }
}
