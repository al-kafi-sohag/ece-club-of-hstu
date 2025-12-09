<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <title> @yield('title', 'Backend') | {{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @include('backend.auth.includes.style-bundle')
    </head>

    <body class="bg-white">
        @include('backend.auth.includes.header')

        <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
            @yield('content')
        </main>

        @include('backend.auth.includes.footer')

        @include('backend.auth.includes.script-bundle')
    </body>
</html>
