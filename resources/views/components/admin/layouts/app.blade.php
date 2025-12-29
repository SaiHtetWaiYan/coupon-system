<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Admin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>
    </head>
    <body class="h-full font-sans antialiased bg-gray-100 dark:bg-gray-900">
        <div class="min-h-full">
            <div class="flex">
                @include('admin.components.sidebar')

                <div class="flex-1 lg:pl-64">
                    @include('admin.components.header')

                    <main class="py-8">
                        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                            @include('admin.components.flash-message')

                            {{ $slot }}
                        </div>
                    </main>
                </div>
            </div>
        </div>

        <script>
            function toggleDarkMode() {
                document.documentElement.classList.toggle('dark');
                localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
            }
        </script>
    </body>
</html>
