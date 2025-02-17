<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Novo Quadro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('boards.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="name" :value="__('Nome do Quadro')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="description" :value="__('Descrição')" />
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                ></textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="background_color" :value="__('Cor de Fundo')" />
                                <input
                                    type="color"
                                    id="background_color"
                                    name="background_color"
                                    value="#f3f4f6"
                                    class="mt-1 block rounded-md border-gray-300 shadow-sm h-10 w-20"
                                >
                                <x-input-error :messages="$errors->get('background_color')" class="mt-2" />
                            </div>

                            <div class="flex justify-end space-x-3">
                                <x-secondary-button onclick="window.history.back()">
                                    {{ __('Cancelar') }}
                                </x-secondary-button>
                                <x-primary-button>
                                    {{ __('Criar Quadro') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
