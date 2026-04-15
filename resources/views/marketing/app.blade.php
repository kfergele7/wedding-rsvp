<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $meta['title'] ?? 'Magic Invitation' }}</title>
    <meta name="description" content="{{ $meta['description'] ?? '' }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $meta['og_title'] ?? ($meta['title'] ?? 'Magic Invitation') }}">
    <meta property="og:description" content="{{ $meta['og_description'] ?? ($meta['description'] ?? '') }}">
    <meta property="og:image" content="{{ $meta['og_image'] ?? '/images/marketing/hero.jpg' }}">
    <meta property="og:url" content="{{ request()->fullUrl() }}">

    @include('partials.favicons')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-wedding-bg text-wedding-black antialiased">
    <script>
        window.__APP_PAGE = @json('marketing-' . $marketingPage);
        window.__APP_PAYLOAD = @json($payload ?? []);
    </script>
    <div id="app"></div>
</body>
</html>
