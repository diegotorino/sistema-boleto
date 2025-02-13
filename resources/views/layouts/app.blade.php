<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://unpkg.com/tippy.js@6/dist/tippy.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/1c82e6ee7d.js" crossorigin="anonymous"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/dark-theme.css'])
</head>
<body class="font-sans antialiased bg-black text-white">
    <div x-data="{ open: false, sidebarOpen: true }" class="min-h-screen">
        <!-- Sidebar -->
        <div x-show="open" class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false"></div>

        <div class="fixed inset-y-0 left-0 z-30 transform transition-all duration-300 lg:translate-x-0 bg-white dark:bg-secondary-dark shadow-lg"
             :class="{
                'w-64': sidebarOpen,
                'w-16': !sidebarOpen,
                'translate-x-0': open,
                '-translate-x-full': !open && !sidebarOpen && window.innerWidth < 1024
             }">
            <!-- Logo -->
            <div class="flex justify-between items-center py-4 px-4" :class="{ 'px-2': !sidebarOpen }">
                <a href="{{ route('dashboard') }}" x-show="sidebarOpen">
                    <img src="{{ asset('images/9f58e5ee-1db5-41a4-9008-fabe207f46b3 (1).png') }}" alt="Logo" style="height: 38px; width: 180px; object-fit: contain;">
                </a>
                <!-- Toggle Sidebar Button (Desktop) -->
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="w-8 h-8 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        :class="{ 'ml-auto': sidebarOpen }">
                    <i class="fas fa-chevron-left text-lg transition-transform duration-200"
                       :class="{ 'transform rotate-180': !sidebarOpen }"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-4" :class="{ 'px-2': sidebarOpen, 'px-1': !sidebarOpen }">
                <div class="space-y-2">
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center px-4 py-2 rounded-lg mb-1 transition-all duration-200 hover:transform hover:translate-x-1 {{ request()->routeIs('dashboard') ? 'bg-accent-color text-white' : 'dark:text-white' }}"
                       :class="{ 'justify-center': !sidebarOpen }"
                       data-tippy-content="Dashboard">
                        <i class="fas fa-chart-line text-xl" :class="{ 'mr-2': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition>Dashboard</span>
                    </a>
                    <a href="{{ route('boletos.index') }}" 
                       class="flex items-center px-4 py-2 rounded-lg mb-1 transition-all duration-200 hover:transform hover:translate-x-1 {{ request()->routeIs('boletos.index') ? 'bg-accent-color text-white' : 'dark:text-white' }}"
                       :class="{ 'justify-center': !sidebarOpen }"
                       data-tippy-content="Boletos">
                        <i class="fas fa-file-invoice-dollar text-xl" :class="{ 'mr-2': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition>Boletos</span>
                    </a>
                    <a href="{{ route('boletos.create') }}" 
                       class="flex items-center px-4 py-2 rounded-lg mb-1 transition-all duration-200 hover:transform hover:translate-x-1 {{ request()->routeIs('boletos.create') ? 'bg-accent-color text-white' : 'dark:text-white' }}"
                       :class="{ 'justify-center': !sidebarOpen }"
                       data-tippy-content="Novo Boleto">
                        <i class="fas fa-plus-circle text-xl" :class="{ 'mr-2': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition>Novo Boleto</span>
                    </a>
                    <a href="{{ route('clientes.index') }}" 
                       class="flex items-center px-4 py-2 rounded-lg mb-1 transition-all duration-200 hover:transform hover:translate-x-1 {{ request()->routeIs('clientes.*') ? 'bg-accent-color text-white' : 'dark:text-white' }}"
                       :class="{ 'justify-center': !sidebarOpen }"
                       data-tippy-content="Clientes">
                        <i class="fas fa-users text-xl" :class="{ 'mr-2': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition>Clientes</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center px-4 py-2 rounded-lg mb-1 transition-all duration-200 hover:transform hover:translate-x-1 {{ request()->routeIs('profile.edit') ? 'bg-accent-color text-white' : 'dark:text-white' }}"
                       :class="{ 'justify-center': !sidebarOpen }"
                       data-tippy-content="Perfil">
                        <i class="fas fa-user-circle text-xl" :class="{ 'mr-2': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition>Perfil</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center px-4 py-2 rounded-lg transition-all duration-200 hover:transform hover:translate-x-1 hover:bg-red-500 hover:text-white dark:text-white"
                                :class="{ 'justify-center': !sidebarOpen }"
                                data-tippy-content="Sair">
                            <i class="fas fa-sign-out-alt text-xl" :class="{ 'mr-2': sidebarOpen }"></i>
                            <span x-show="sidebarOpen" x-transition>Sair</span>
                        </button>
                    </form>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="transition-all duration-300" :class="{ 'lg:pl-64': sidebarOpen, 'lg:pl-16': !sidebarOpen }">
            <!-- Top Navigation -->
            <div class="sticky top-0 z-10 flex h-16 bg-white dark:bg-secondary-dark border-b border-gray-200 dark:border-gray-700 lg:hidden">
                <button @click="open = true" class="px-4 text-gray-500 dark:text-gray-400 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 focus:text-gray-600 dark:focus:text-gray-300 lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <!-- Page Content -->
            <main class="p-4">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script>
        // Inicializa os tooltips
        document.addEventListener('DOMContentLoaded', function() {
            tippy('[data-tippy-content]', {
                placement: 'right',
                arrow: true,
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                delay: [100, 0]
            });
        });
    </script>

    <!-- Notificações -->
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => { show = false }, 3000)"
         x-show="show"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-0 right-0 m-4 z-50"
    >
        <div x-show="type === 'success'" class="bg-green-500 text-white px-4 py-3 rounded shadow-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span x-text="message"></span>
            </div>
        </div>
        <div x-show="type === 'error'" class="bg-red-500 text-white px-4 py-3 rounded shadow-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span x-text="message"></span>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
