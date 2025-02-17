<div>
    <!-- Botão para abrir o modal -->
    <button 
        wire:click="$set('showModal', true)"
        class="h-32 w-64 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-lg border-2 border-white/20 hover:border-white/30 transition-all duration-200"
    >
        <div class="text-center">
            <i class="fas fa-plus text-2xl mb-2"></i>
            <p>Criar novo quadro</p>
        </div>
    </button>

    <!-- Modal -->
    <div
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4">
            <!-- Overlay -->
            <div 
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/50"
            ></div>

            <!-- Modal Content -->
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                class="relative bg-white dark:bg-gray-800 rounded-lg w-full max-w-md p-6"
            >
                <div class="absolute top-4 right-4">
                    <button 
                        wire:click="$set('showModal', false)"
                        class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Criar novo quadro</h2>

                <form wire:submit.prevent="createBoard">
                    <div class="space-y-4">
                        <!-- Nome -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Nome do quadro
                            </label>
                            <input 
                                type="text"
                                id="name"
                                wire:model="name"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-accent-color focus:ring-accent-color"
                                required
                            >
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Descrição -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Descrição (opcional)
                            </label>
                            <textarea
                                id="description"
                                wire:model="description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-accent-color focus:ring-accent-color"
                            ></textarea>
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Cor de Fundo -->
                        <div>
                            <label for="background_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Cor de fundo
                            </label>
                            <div class="mt-1 flex items-center space-x-2">
                                <input
                                    type="color"
                                    id="background_color"
                                    wire:model="background_color"
                                    class="h-8 w-8 rounded border-gray-300 dark:border-gray-600"
                                >
                                <div 
                                    class="h-8 w-16 rounded"
                                    style="background-color: {{ $background_color }}"
                                ></div>
                            </div>
                            @error('background_color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            type="button"
                            wire:click="$set('showModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-accent-color hover:bg-accent-color-dark rounded-md"
                        >
                            Criar quadro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
