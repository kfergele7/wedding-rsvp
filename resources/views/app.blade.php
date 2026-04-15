<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $payload['siteTitle'] ?? config('app.name', 'Magic Invitation') }}</title>
    @include('partials.favicons')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <script>
        window.__APP_PAGE = @json($page ?? 'home');
        window.__APP_PAYLOAD = @json($payload ?? []);
    </script>
    <div id="app"></div>
</body>
</html>
