<x-guest-layout>
    <div class="flex flex-col items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-secondary-dark shadow-md overflow-hidden sm:rounded-lg">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold mb-2 text-white">Bem-vindo de volta!</h1>
                <p class="text-sm text-gray-400">Entre com suas credenciais para acessar o sistema</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div class="animate__animated animate__fadeInLeft animate__delay-1s">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" 
                                class="sm:text-sm"
                                type="email" 
                                name="email" 
                                :value="old('email')" 
                                required 
                                autofocus 
                                autocomplete="username"
                                icon="envelope" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="animate__animated animate__fadeInLeft animate__delay-2s">
                    <x-input-label for="password" :value="__('Senha')" />
                    <x-text-input id="password" 
                                class="sm:text-sm"
                                type="password"
                                name="password"
                                required 
                                autocomplete="current-password"
                                icon="lock" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between animate__animated animate__fadeInLeft animate__delay-3s">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" 
                               type="checkbox" 
                               class="form-checkbox rounded border-gray-300" 
                               name="remember">
                        <span class="ms-2 text-sm text-gray-400">{{ __('Lembrar-me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-gray-400 hover:text-accent transition-colors" 
                           href="{{ route('password.request') }}">
                            {{ __('Esqueceu sua senha?') }}
                        </a>
                    @endif
                </div>

                <div class="mt-6 animate__animated animate__fadeInUp animate__delay-4s">
                    <button type="submit" 
                            class="w-full flex justify-center items-center px-4 py-2 bg-accent hover:bg-accent-dark text-white font-semibold rounded-lg transition duration-150 ease-in-out">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        {{ __('Entrar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
