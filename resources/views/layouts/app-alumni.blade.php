<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Alumni View' }}</title>

    <link rel="icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        {{-- ...your fallback <style> block, unchanged... --}}
    @endif
    @livewireStyles
</head>

<body class="bg-[#F0F2F5] dark:bg-[#18191A] antialiased">
    @include('components.headers.alumni.header')

    <main>
        <div class="select-none">
        {{ $slot }}
        </div>
    </main>

    @include('components.footers.alumni.footer')
    @stack('scripts')
    @livewireScripts
    <script src="https://unpkg.com/preline/dist/preline.js"></script>
</body>


</html>
