<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
{{--    <script src="https://cdn.tailwindcss.com"></script>--}}
    <meta name="description" content="{{$description ?? 'Welcome to the Vinyl Shop'}}">
    <title>The Vinyl Shop: {{$title ?? 'The Vinyl Shop'}}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
    <x-layout.favicons/>
</head>
<body class="font-sans antialiased">
<div class="flex flex-col space-y-4 min-h-screen text-gray-800 bg-gray-100">
    <header class="shadow bg-white/70 sticky inset-0 backdrop-blur-sm z-10">
        {{--  Navigation  --}}
        @livewire('layout.nav-bar')
    </header>
    <main class="container mx-auto p-4 flex-1 px-4">
        {{-- Title --}}
        <h1 class="text-3xl mb-4">
            {{$subtitle ?? $title ?? "This page has no (sub)title"}}
        </h1>
        {{-- Main content --}}
        {{$slot}}
    </main>
    <x-layout.footer/>
</div>
@stack('script')
@livewireScripts
</body>
</html>
