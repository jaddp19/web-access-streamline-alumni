<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Alumni View' }}</title>

    <link rel="icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png">

    {{-- Theme init — must run before styles/paint to avoid a flash of the wrong theme --}}
    <script>
        (function () {
            const saved = localStorage.getItem('hs_theme'); // Preline's default storage key
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = saved === 'dark' || (!saved && prefersDark);
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>

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

    <main class="bg-white dark:bg-black">
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
