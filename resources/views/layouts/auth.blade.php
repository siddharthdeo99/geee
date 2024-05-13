<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}"  x-data="{ theme: $persist('light') }" :class="{ 'dark': theme === 'dark', 'classic': theme === 'classic' }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf_token" value="{{ csrf_token() }}"/>
        <!-- Scripts -->
        <title>{{ $title ?? $generalSettings->site_name ?: config('app.name', 'AdFox') }}</title>

        <meta
            name="description"
            content="{{ $description ?? $generalSettings->site_description }}"
        />
        <link rel="icon" type="image/png" href="{{ getSettingMediaUrl('general.favicon_path', 'favicon', asset('images/favicon.png')) }}">

         <!-- PWA  -->
        <meta name="theme-color" content="#6777ef"/>
        <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}"  >

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">

        @filamentStyles
        @vite('resources/css/app.css')
        <!-- Insert Custom Script in Head -->
        {!! $scriptSettings->custom_script_head !!}
    </head>
    <body class="font-sans bg-gray-50 antialiased dark:bg-gray-950 dark:text-white classic:bg-gray-100">
        <div class="flex min-h-screen">
            <!-- Left Section -->
            <div class="md:w-1/2 md:flex md:flex-col p-8 w-full">
                {{ $slot }}
            </div>

            <!-- Right Section -->
            <div class="w-1/2 hidden md:flex items-center justify-center bg-white border-l border-gray-200 dark:border-white/10 dark:bg-gray-900 classic:border-black">
                <img src="{{ asset('/images/auth.svg') }}" alt="Image" class="max-w-full h-auto" />
            </div>
        </div>

        @if($authSettings->recaptcha_enabled)
            {!! GoogleReCaptchaV3::init() !!}
        @endif


        @filamentScripts
        @vite('resources/js/app.js')
        @stack('scripts')

         <!-- Insert Custom Script in Body -->
         {!! $scriptSettings->custom_script_body !!}

        <script>
            if ("serviceWorker" in navigator) {
                // Register a service worker hosted at the root of the
                // site using the default scope.
                navigator.serviceWorker.register("/sw.js").then(
                (registration) => {
                    console.log("Service worker registration succeeded:", registration);
                },
                (error) => {
                    console.error(`Service worker registration failed: ${error}`);
                },
                );
            } else {
                console.error("Service workers are not supported.");
            }
        </script>
    </body>
</html>

