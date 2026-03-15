<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel with Vue</title>
    @vite(['resources/js/app.js'])
</head>
<body>
    <div id="app">
        <!-- Vue components will mount here -->
        <h1>Hello from Vue!</h1>
    </div>
</body>
</html>