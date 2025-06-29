<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Footballer Profiles')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 min-h-screen overflow-hidden">

<!-- Main Content -->
<main>
    <!-- Header -->
    <div class="text-center mb-8 mt-4">
        <a href="{{ route("welcome") }}"><p class="text-2xl text-white font-extrabold">FUTEMON</p></a>
    </div>

    @yield('content')
</main>
@livewireScripts
</body>
</html>
