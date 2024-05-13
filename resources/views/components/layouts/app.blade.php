<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}"  dir="{{ config()->get('direction') }}" x-data="{ theme: $persist('light'), isMobile: window.innerWidth < 1024 }" x-init="theme = new URL(window.location.href).searchParams.get('theme') || theme"  :class="{ 'dark': theme === 'dark', 'classic': theme === 'classic' }" @resize.window="isMobile = window.innerWidth < 1024">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf_token" value="{{ csrf_token() }}"/>

        {{-- Generate seo tags --}}
        {!! SEO::generate() !!}
        {!! JsonLd::generate() !!}


        <link rel="icon" type="image/png" href="{{ getSettingMediaUrl('general.favicon_path', 'favicon', asset('images/favicon.png')) }}">


        <link rel="preconnect" href="https://fonts.googleapis.com">

        <!-- PWA  -->
        <meta name="theme-color" content="#6777ef"/>
        <link rel="apple-touch-icon" href="{{ getSettingMediaUrl('general.favicon_path', 'favicon', asset('images/favicon.png')) }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}"  >


        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">

        @filamentStyles
        @vite('resources/css/app.css')
        <!-- Insert Custom Script in Head -->
        {!! $scriptSettings->custom_script_head !!}

        {!! GoogleReCaptchaV3::init() !!}
        
         {{-- Styles --}}
         @stack('styles')
    </head>
    <body x-on:close-modal.window="document.documentElement.classList.add('flow-auto')" x-on:open-modal.window="document.documentElement.classList.remove('flow-auto')" class="bg-gray-50  font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white classic:bg-gray-100 classic:text-black">

        @livewire('notifications')

        {{ $slot }}

        @if($generalSettings->cookie_consent_enabled)
            <x-cookie-consent />
        @endif

        @filamentScripts
        <!-- Insert Custom Script in Body -->
        {!! $scriptSettings->custom_script_body !!}

        <script>
             document.addEventListener('livewire:init', () => {
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.register('/sw.js').then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    }, function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
                }

            })
        </script>
        @stack('scripts')
    </body>
</html>
