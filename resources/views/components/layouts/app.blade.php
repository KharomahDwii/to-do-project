<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Laravel' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin: 0; padding: 0; min-height: 100vh;">
    <div>
        {{ $slot }}
        @stack('scripts')
    </div>
    @livewireScripts
    <script>
        // Fix untuk Livewire + Alpine conflict
        document.addEventListener('livewire:init', () => {
            Livewire.hook('message.processed', (message, component) => {
                // Refresh Alpine components setelah update Livewire
                if (window.Alpine && typeof window.Alpine.mutateDom === 'function') {
                    window.Alpine.mutateDom(() => {});
                }
            });
        });
    </script>
</body>
</html>