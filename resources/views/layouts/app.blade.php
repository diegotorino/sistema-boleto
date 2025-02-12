<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div x-data="{ open: false }">
        <!-- Sidebar -->
        <div x-show="open" class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden" @click="open = false"></div>

        <div class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r transform transition-transform duration-200 lg:transform-none lg:translate-x-0"
             :class="{'translate-x-0': open, '-translate-x-full': !open}">
            <!-- Logo -->
            <div class="h-16 flex items-center justify-between px-4 border-b">
                <span class="text-xl font-semibold">{{ config('app.name', 'Laravel') }}</span>
                <button @click="open = false" class="lg:hidden">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-4 px-2">
                <a href="{{ route('boletos.index') }}" class="block px-4 py-2 rounded-lg {{ request()->routeIs('boletos.index') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">
                    Boletos
                </a>
                <a href="{{ route('boletos.create') }}" class="block px-4 py-2 rounded-lg {{ request()->routeIs('boletos.create') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">
                    Novo Boleto
                </a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 rounded-lg {{ request()->routeIs('profile.edit') ? 'bg-gray-100' : 'hover:bg-gray-50' }}">
                    Perfil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-50">
                        Sair
                    </a>
                </form>
            </nav>
        </div>

        <!-- Content -->
        <div class="lg:pl-64">
            <!-- Header -->
            <div class="sticky top-0 z-10 bg-white border-b">
                <div class="flex h-16 items-center justify-between px-4">
                    <button @click="open = !open" class="lg:hidden">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    @if (isset($header))
                        {!! $header !!}
                    @endif
                </div>
            </div>

            <!-- Main content -->
            <main class="p-4">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
