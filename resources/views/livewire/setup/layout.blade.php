<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}"  >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf_token" value="{{ csrf_token() }}"/>

        <!-- Scripts -->
        <title>{{ config('app.name', 'AdFox') }}</title>


        <link rel="preconnect" href="https://fonts.googleapis.com">

        <!-- PWA  -->
        <meta name="theme-color" content="#6777ef"/>
        <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}"  >


        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">

        @filamentStyles
        @vite('resources/css/app.css')
    </head>
    <body class="bg-gray-50  font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white classic:bg-gray-100 classic:text-black">


        @yield('content')

        @filamentScripts
        @stack('scripts')
    </body>
</html>
