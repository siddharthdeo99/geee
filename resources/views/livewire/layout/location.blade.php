<div class="flex items-center md:flex-row-reverse justify-end" x-data>
    <span class="ml-2 whitespace-nowrap cursor-pointer line-clamp-1 w-48 text-right md:text-left" @click="$store.location.open = true; document.body.style.overflow = 'hidden'">{{ $locationName }}</span>


    <x-icon-location class="w-6 h-6 cursor-pointer dark:text-gray-500" @click="$store.location.open = true; document.body.style.overflow = 'hidden'"/>


    <!-- Modal -->
    <div x-show="$store.location.open" class="fixed inset-0 flex items-start md:pt-20 justify-center z-50 bg-black dark:bg-opacity-90 bg-opacity-50" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"

        >
        <div @click.away="$store.location.open = false; document.body.style.overflow = 'auto'" class="bg-white md:rounded-xl w-[40rem] h-full md:h-auto dark:bg-gray-800 dark:border-white/10 dark:border">
            <!-- Modal Header -->
            <div class="flex justify-between items-center px-6 py-4">
                <h2 class="text-center text-lg md:text-xl">{{ __('messages.t_where_search') }}</h2>
                <button @click="$store.location.open = false; document.body.style.overflow = 'auto'" class="text-gray-400 hover:text-gray-600">
                    <x-icon-close class="w-4 h-4 md:w-5 md:h-5 classic:text-black"/>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="bg-gray-50 dark:bg-gray-950 px-6 py-6 rounded-b-xl h-full md:h-auto classic:bg-gray-100 ">
                <input id="location-input" type="text" placeholder="{{ __('messages.t_address_city_province') }}" class="focus:ring-black focus:border-black w-full px-4 py-3 border border-gray-200 dark:border-white/10 rounded-xl rounded-b-none bg-white classic:border-black dark:bg-gray-800" @input="$store.location.updateLocations($event.target.value)">
                <div class="border-x border-b border-gray-200 dark:border-white/10 rounded-b-xl bg-white classic:border-black dark:bg-gray-800">
                    <!-- Current Location Section -->
                    <div @click='$store.location.getLocation($wire)' class="flex items-center  gap-x-2 px-4 py-3 cursor-pointer">
                        <x-icon-location class="w-6 h-6 dark:text-gray-500"/>
                        <div>
                        <span>{{ __('messages.t_use_current_location') }}</span>

                        <div x-data="{ locationBlocked: @entangle('locationBlocked') }" class="text-gray-400 text-sm" x-show="locationBlocked">{{ __('messages.t_location_blocked') }}</div>
                        </div>
                    </div>
                    <!-- Locations from Google -->
                    <div class="px-4 border-t border-gray-200 dark:border-white/10 classic:border-black" x-show="$store.location.locations && $store.location.locations.length > 0">
                        <template x-for="(location, index) in $store.location.locations" :key="index">
                            <div @click="$store.location.selectLocation(location, $wire, 'location-redirect'); $store.location.open = false; document.body.style.overflow = 'auto'; " class="flex items-center my-4 cursor-pointer" >
                                <span class="ml-2" x-text="location.description"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@script
<script>
        let allowedCountries = @json($locationSettings->allowed_countries);

        Alpine.store('location', {
            open: false,
            init() {
                let locationName = $wire.$get('locationSlug');
                if(locationName) {
                    // Call the geocoding API to fetch the location name
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${locationName}&limit=1`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            this.locations = data.map(item => {
                                const description = `${item.display_name}`;
                                const postalCode = item.addresstype === 'postcode' ? item.name : false;

                                return {
                                    name: item.display_name,
                                    addresstype: item.addresstype,
                                    lat: item.lat,
                                    lon: item.lon,
                                    postalCode,
                                    description
                                };
                            });
                            if (this.locations.length > 0) {
                                const firstResult = this.locations[0];
                                $store.location.selectLocation(firstResult, $wire);
                            }
                        } else {
                            console.error('No results found');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            },
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

                const allowedCountryCodes = allowedCountries.join(',');

                // Use Nominatim API for geocoding
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${inputValue}&countrycodes=${allowedCountryCodes}&limit=7`)

                    .then(response => response.json())
                    .then(data => {
                        this.locations = data.map(item => {
                            const description = `${item.display_name}`;
                            const postalCode = item.addresstype == 'postcode' ? item.name : false; // Extract the postal code if available

                            return {
                                name: item.display_name,
                                addresstype: item.addresstype,
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
                const addressType = selectedLocation.addresstype;

                // Use Nominatim’s reverse geocoding API to get detailed address components
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        const address = data.address;
                        const city = address.city || address.town || address.village || address.suburb || address.county;
                        const country = address.country;
                        const state = address.state;
                        const postalCode = selectedLocation.postalCode ? selectedLocation.postalCode : address.postcode;
                        const locationName = displayName;
                        let locationType;
                        let locationSlug;
                        if (addressType == 'city') {
                            locationType = 'city';
                            locationSlug = city;
                        } else if (addressType == 'state') {
                            locationType = 'state';
                            locationSlug = state;
                        } else if (addressType == 'country') {
                            locationType = 'country';
                            locationSlug = country;
                        } else {
                            locationSlug = address.city || address.town || address.village || address.suburb || address.county ;
                        }
                        wire.set('locationName', locationName);
                        wire.set('latitude', latitude);
                        wire.set('longitude', longitude);
                        wire.set('locationDisplayName', displayName);

                        wire.storeLocationInSession(latitude, longitude, locationName, country, state, city, locationType);

                        if(type == 'location-redirect') {
                            $wire.dispatch('location-redirect', { locationSlug });
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching detailed location:", error);
                    });
            }

        });

</script>
@endscript
