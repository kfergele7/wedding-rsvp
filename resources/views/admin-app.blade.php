<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | Wedding RSVP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @php
        $adminPayload = array_merge($payload ?? [], [
            'adminSection' => $page ?? 'dashboard',
            'logoutUrl' => route('admin.logout'),
        ]);
    @endphp

    <script>
        window.__APP_PAGE = 'admin';
        window.__APP_PAYLOAD = @json($adminPayload);
    </script>
    <div id="app"></div>
</body>
</html>
