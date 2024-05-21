<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
        
        <!-- Add localeUrls object -->
        <script>
            window.localeUrls = {
                en: "{{ route('locale.switch', 'en') }}",
                nl: "{{ route('locale.switch', 'nl') }}"
            };
        </script>
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
