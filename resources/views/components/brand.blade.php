<a href="/">
    <style>
        @media (max-width: 767px) {
            .custom-logo {
                height: {{ $generalSettings->logo_height_mobile }}rem;
            }
        }

        @media (min-width: 768px) {
            .custom-logo {
                height: {{ $generalSettings->logo_height_desktop }}rem;
            }
        }
    </style>
    <img src="{{ getSettingMediaUrl('general.logo_path', 'logo', asset('images/logo.svg')) }}" alt="AdFox"
         class="custom-logo dark:filter dark:invert" />
</a>
