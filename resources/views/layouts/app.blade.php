<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf_token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="{{ asset('/img/favicon.ico') }}" type="image/x-icon">
    <style>
        .notification-dot {
            height: 10px;
            width: 10px;
            background-color: red;
            border-radius: 50%;
            display: inline-block;
            position: absolute;
            top: -5px;
            right: -5px;
        }
    </style>
    @vite(['resources/js/app.js'])

    {{-- Internal Javascript --}}
    @stack('scripts')

    <title>Internity</title>
</head>

<body class="antialiased g-sidenav-show">
    <div class="content">
        @yield('content')
    </div>
</body>

</html>
