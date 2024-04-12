<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/charts.css/dist/charts.min.css">

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif


            <!-- Messages -->
            <div class="w-[30%] m-auto text-center  text-white mt-2 text-capitalize">
                    @session('success')
                        <div class="bg-green-400 rounded-lg shadow-lg lg:col-span-3 lg:p-4 p-2">
                            {{ Str::ucfirst(session('success', '')) }}
                        </div>
                    @endsession
                    @if($errors->any())
                        <div class="bg-red-400 rounded-lg shadow-lg lg:col-span-3 lg:p-4 p-2">
                            {{ Str::ucfirst('they was an error') }}
                        </div>
                    @endif
            </div>

            <!-- Page Content -->
            <main>
                {{-- Yield Content --}}
                @yield('content')
            </main>
        </div>

        <!-- Script -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    </body>
</html>
