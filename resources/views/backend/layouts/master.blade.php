<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <title> @yield('title', 'Backend') | {{ config('app.name', 'Laravel') }}</title>
        @php $page_slug = isset($page_slug) ? $page_slug : ''; @endphp

        @vite(['resources/css/backend/app.css', 'resources/js/backend/app.js'])

        {{-- Temporarily added jQuery --}}
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script>
            window.$ = window.jQuery = jQuery;
        </script>

        @include('backend.includes.style-bundle')
    </head>

    <body class="sidebar-expand-lg bg-body-tertiary">
        <div class="app-wrapper">
            @include('backend.includes.header')
            @include('backend.includes.sidebar')

            <main class="app-main" id="main">
                @yield('content')
            </main>

            @include('backend.includes.footer')
        </div>

        @include('backend.includes.script-bundle')
        @include('backend.includes.cropper-modal')
    </body>
</html>
