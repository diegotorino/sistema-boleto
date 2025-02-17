<nav x-data="{ open: false }" class="dark:bg-secondary-dark border-b dark:border-border-color">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-accent-color" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="fas fa-chart-line mr-2"></i> {{ __('Painel') }}
                    </x-nav-link>
                    <x-nav-link :href="route('boletos.index')" :active="request()->routeIs('boletos.index')">
                        <i class="fas fa-file-invoice-dollar mr-2"></i> {{ __('Boletos') }}
                    </x-nav-link>
                    <x-nav-link :href="route('boletos.create')" :active="request()->routeIs('boletos.create')">
                        <i class="fas fa-plus-circle mr-2"></i> {{ __('Novo Boleto') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md dark:text-gray-400 dark:hover:text-white focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="dark:text-gray-300 dark:hover:bg-border-color">
                            <i class="fas fa-user-circle mr-2"></i> {{ __('Meu Perfil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="dark:text-gray-300 dark:hover:bg-border-color">
                                <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Menu Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md dark:text-gray-400 hover:text-accent-color dark:hover:text-accent-color focus:outline-none focus:text-accent-color dark:focus:text-accent-color transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu Mobile -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="dark:text-gray-300 dark:hover:bg-border-color">
                <i class="fas fa-chart-line mr-2"></i> {{ __('Painel') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('boletos.index')" :active="request()->routeIs('boletos.index')" class="dark:text-gray-300 dark:hover:bg-border-color">
                <i class="fas fa-file-invoice-dollar mr-2"></i> {{ __('Boletos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('boletos.create')" :active="request()->routeIs('boletos.create')" class="dark:text-gray-300 dark:hover:bg-border-color">
                <i class="fas fa-plus-circle mr-2"></i> {{ __('Novo Boleto') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t dark:border-border-color">
            <div class="px-4">
                <div class="font-medium text-base dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm dark:text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="dark:text-gray-300 dark:hover:bg-border-color">
                    <i class="fas fa-user-circle mr-2"></i> {{ __('Meu Perfil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="dark:text-gray-300 dark:hover:bg-border-color">
                        <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Sair') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
