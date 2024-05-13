<div class="mt-4">
    <p class="mb-4">{{ __('messages.t_confirm_location') }}</p>


    <x-input-error :messages="$errors->get('locationName')" class="mb-4" />

    <div class="rounded-b" x-data>
        <div class="relative">
            <input wire:model='locationName' id="location-input" type="text" placeholder="{{ __('messages.t_type_location_prompt') }}" class="focus:ring-black focus:border-black w-full px-4 py-3 border border-gray-200 rounded rounded-b-none bg-white dark:bg-gray-900  dark:border-white/10 classic:border-black" @input="$store.postad.updateLocations($event.target.value)">
            @if($locationName)
                <button wire:click="$set('locationName', '')" class="p-2 absolute right-2 top-1.5 bg-white dark:bg-gray-900">
                    <x-heroicon-o-x-mark class="w-6 h-6"/>
                </button>
            @endif
        </div>
        <div class="border-x border-b border-gray-200 rounded-b bg-white dark:bg-gray-900 dark:border-white/10 classic:border-black">
            <!-- Current Location Section -->
            <div @click="$store.postad.getLocation($wire)" class="flex items-center  gap-x-2 px-4 py-3 cursor-pointer">
                <x-icon-location class="w-6 h-6"/>
                <div>
                <span>{{ __('messages.t_use_current_location') }}</span>
                <div x-data="{ locationBlocked: @entangle('locationBlocked') }" class="text-gray-400 text-sm" x-show="locationBlocked">{{ __('messages.t_location_blocked') }}</div>
                </div>
            </div>
            <!-- Locations from Google -->
            <div class="px-4 border-t border-gray-200  dark:border-white/10 classic:border-black" x-show="$store.postad.locations && $store.postad.locations.length > 0">
            <template x-for="(location, index) in $store.postad.locations" :key="index">

                    <div @click="$store.postad.selectLocation(location, $wire, 'post-ad')" class="flex items-center my-4 cursor-pointer" >
                        <span class="ml-2" x-text="location.description"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>


@script
<script>
        Alpine.store('postad', {
            open: false,
            locations: [],
            getLocation(wire) {
                navigator.geolocation.getCurrentPosition(position => {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    // Call Nominatim reverse geocoding API to fetch the location name
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.address) {
                            const address = data.address;
                            const city = address.city || address.town || address.village || address.county;
                            const state = address.state;
                            const country = address.country;
                            const postalCode = address.postcode;
                            const locationName = `${city ? city + ', ' : ''}${state ? state + ', ' : ''} ${postalCode}, ${country}`;

                            wire.set('latitude', latitude);
                            wire.set('longitude', longitude);
                            wire.set('locationName', locationName);
                            wire.set('locationDisplayName', data.display_name);
                            wire.set('locationBlocked', false);
                            wire.set('city', city);
                            wire.set('state', state);
                            wire.set('country', country);
                            wire.set('postal_code', postalCode);
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching the location name:", error);
                    });

                }, () => {
                    wire.set('locationBlocked', true);
                });
            },
            updateLocations(inputValue) {
                if (!inputValue) {
                    this.locations = [];
                    return;
                }

                // Use Nominatim API for geocoding
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${inputValue}&limit=10`)
                    .then(response => response.json())
                    .then(data => {
                        this.locations = data.map(item => {
                            const description = `${item.display_name}`;
                            const postalCode = item.addresstype == 'postcode' ? item.name : false; // Extract the postal code if available

                            return {
                                name: item.display_name,
                                lat: item.lat,
                                lon: item.lon,
                                postalCode, // Add postal code to the returned object
                                description
                            };
                        });

                    })
                    .catch(error => {
                        console.error("Error fetching locations:", error);
                        this.locations = [];
                    });
            },

            selectLocation(selectedLocation, wire, type) {
                const latitude = selectedLocation.lat;
                const longitude = selectedLocation.lon;
                const displayName = selectedLocation.name;

                // Use Nominatim’s reverse geocoding API to get detailed address components
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        const address = data.address;
                        const city = address.city || address.town || address.village || address.suburb || address.county;
                        const country = address.country;
                        const state = address.state;

                        const postalCode = selectedLocation.postalCode ? selectedLocation.postalCode : address.postcode;
                       // Create an array with all location components
                       const locationComponents = [city, state, postalCode, country];

                        // Filter out undefined or empty components and join them with a comma
                        const locationName = locationComponents.filter(component => component).join(', ');


                        wire.set('locationName', locationName);
                        wire.set('latitude', latitude);
                        wire.set('longitude', longitude);
                        wire.set('locationDisplayName', displayName);
                        wire.set('city', city);
                        wire.set('country', country);
                        wire.set('state', state);
                        wire.set('postal_code', postalCode);
                    })
                    .catch(error => {
                        console.error("Error fetching detailed location:", error);
                    });
            }


        });
</script>
@endscript
