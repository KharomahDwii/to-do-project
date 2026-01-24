<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Laravel' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background: #26324e; margin: 0; padding: 0; min-height: 100vh;">
    <div class="container mx-auto px-4 py-8">
        {{ $slot }}
        @stack('scripts')
    </div>
    @livewireScripts
</body>
</html>