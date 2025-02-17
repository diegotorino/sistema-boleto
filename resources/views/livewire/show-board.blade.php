<div>
    <!-- Modal -->
    <div
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        @if($board)
        <div class="min-h-screen" style="background-color: {{ $board->background_color }}">
            <!-- Cabeçalho -->
            <div class="bg-black/30">
                <div class="mx-auto max-w-7xl px-6 lg:px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white">{{ $board->name }}</h2>
                            @if($board->description)
                                <p class="mt-1 text-sm text-white/80">
                                    {{ $board->description }}
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-4">
                            <button 
                                wire:click="$set('showModal', false)"
                                class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="relative" x-data="{ open: false }">
                                <button 
                                    @click="open = !open"
                                    class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors"
                                >
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <div 
                                    x-show="open" 
                                    @click.away="open = false"
                                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 z-10"
                                >
                                    <a href="{{ route('boards.edit', $board) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i class="fas fa-edit mr-2"></i>{{ __('Editar Quadro') }}
                                    </a>
                                    <form action="{{ route('boards.destroy', $board) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700"
                                            onclick="return confirm('Tem certeza que deseja excluir este quadro?')"
                                        >
                                            <i class="fas fa-trash mr-2"></i>{{ __('Excluir Quadro') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conteúdo Principal -->
            <div class="p-6">
                @livewire('task-board', ['board' => $board])
            </div>
        </div>
        @endif
    </div>
</div>
