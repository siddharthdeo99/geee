<?php

namespace App\Livewire\Layout;

use App\Settings\LocationSettings;
use Livewire\Component;
use App\Models\Country;
use App\Settings\GoogleLocationKitSettings;
use Livewire\Attributes\Reactive;

/**
 * Location Component.
 *
 * Represents the location selector and functionalities related to
 * managing and storing user's location preferences.
 */
class Location extends Component
{
    // Latitude of the selected location.
    public $latitude;

    // Longitude of the selected location.
    public $longitude;

    // Display name of the selected location.
    public $locationName = 'All locations';

    // Indicates if the user has blocked the location access.
    public $locationBlocked = false;

    #[Reactive]
    public $locationSlug;

    /**
     * Initialize the component.
     *
     * Set the default location based on the system settings or user's session.
     */
    public function mount()
    {
        $default_country = app(LocationSettings::class)->default_country;
        $this->locationName = Country::where('code', $default_country)->first()->name;
        $this->locationName = session('locationName',  $this->locationName);
    }

    /**
     * Store the selected location details in the user's session.
     *
     * @param float  $latitude
     * @param float  $longitude
     * @param string $locationName
     * @param string $country
     * @param string $state
     * @param string $city
     * @param string $locationType
     */
    public function storeLocationInSession($latitude, $longitude, $locationName, $country, $state, $city, $locationType)
    {
        session([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'locationName' => $locationName,
            'country' => $country,
            'state' => $state,
            'city' => $city,
            'locationType' => $locationType
        ]);
        $this->dispatch('location-updated');
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
     * Update the component's latitude and longitude properties based on selected location.
     *
     * @param array $data Associative array containing 'latitude' and 'longitude' keys.
     */
    public function selectLocation($data)
    {
        $this->latitude = $data['latitude'];
        $this->longitude = $data['longitude'];
    }

    /**
     * Render the location view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
      // Determine which view to render based on the presence of the 'google-location-kit' plugin
      $view = app('filament')->hasPlugin('google-location-kit') && app(GoogleLocationKitSettings::class)->status  ? 'google-location-kit::layout.location' : 'livewire.layout.location';
      return view($view);
    }

}
