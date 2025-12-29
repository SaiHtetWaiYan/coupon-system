<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Coupon System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>
    </head>
    <body class="font-sans text-gray-900 antialiased dark:text-gray-100">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-100 via-white to-purple-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
            <div class="mb-6 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-600 mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185zM9.75 9h.008v.008H9.75V9zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 4.5h.008v.008h-.008V13.5zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Coupon System</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage and redeem your coupons</p>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl overflow-hidden sm:rounded-2xl dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                {{ $slot }}
            </div>

            <p class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} Coupon System. All rights reserved.
            </p>
        </div>
    </body>
</html>
