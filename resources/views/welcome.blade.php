<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body>
    <div class="container mt-5">
        @livewire('todo-list')
    </div>

    @livewireScripts
    @vite('resources/js/app.js')
</body>
</html>