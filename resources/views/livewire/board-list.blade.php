<div x-data="{ 
    showModal: false,
    selectedBoard: null,
    openBoard(boardId) {
        @this.openBoard(boardId).then(result => {
            this.selectedBoard = result;
            this.showModal = true;
        });
    }
}">
    <!-- Grid de Quadros -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Componente de Criar Quadro -->
        <livewire:create-board />

        <!-- Cards dos Quadros Existentes -->
        @foreach($boards as $board)
            <div 
                @click="openBoard({{ $board->id }})"
                class="cursor-pointer"
            >
                <div class="h-32 rounded-lg overflow-hidden hover:ring-2 ring-white/30 transition-all duration-200" style="background-color: {{ $board->background_color }}">
                    <div class="h-full p-4 bg-black/10 hover:bg-black/0 transition-colors duration-200">
                        <h3 class="text-lg font-semibold text-white">
                            {{ $board->name }}
                        </h3>
                        @if($board->description)
                            <p class="mt-1 text-sm text-white/80">
                                {{ Str::limit($board->description, 60) }}
                            </p>
                        @endif
                        <div class="mt-2 flex items-center space-x-2 text-white/60 text-sm">
                            <span>{{ $board->tasks->count() }} tarefas</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal do Quadro -->
    <div 
        x-show="showModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-hidden"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <!-- Overlay -->
        <div 
            class="absolute inset-0 bg-black/50"
            @click="showModal = false"
        ></div>

        <!-- Modal Content -->
        <div 
            class="absolute inset-0 overflow-hidden"
            x-show="showModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
        >
            <template x-if="selectedBoard">
                <div class="h-full" :style="'background-color: ' + selectedBoard.background_color">
                    <!-- Cabeçalho -->
                    <div class="bg-black/30">
                        <div class="mx-auto max-w-7xl px-6 lg:px-8 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-2xl font-bold text-white" x-text="selectedBoard.name"></h2>
                                    <p class="mt-1 text-sm text-white/80" x-text="selectedBoard.description"></p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <button 
                                        @click="showModal = false"
                                        class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conteúdo -->
                    <div class="p-6">
                        <div class="flex space-x-4 overflow-x-auto pb-4">
                            <template x-for="list in selectedBoard.lists" :key="list.id">
                                <div class="flex-shrink-0 w-72">
                                    <div class="bg-white/10 rounded-lg">
                                        <div class="p-3 flex items-center justify-between">
                                            <h3 class="font-medium text-white text-sm">
                                                <span x-text="list.name"></span>
                                                <span class="text-white/60" x-text="'(' + list.tasks.length + ')'"></span>
                                            </h3>
                                        </div>
                                        <div class="px-2 pb-2 space-y-2">
                                            <template x-for="task in list.tasks" :key="task.id">
                                                <div class="bg-white/10 hover:bg-white/20 p-3 rounded cursor-pointer">
                                                    <h4 class="font-medium text-white mb-2" x-text="task.title"></h4>
                                                    <div class="flex items-center space-x-4 text-sm text-white/60">
                                                        <div class="flex items-center space-x-1">
                                                            <template x-if="task.priority === 'high'">
                                                                <div class="flex items-center space-x-1">
                                                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                                                    <span>Alta</span>
                                                                </div>
                                                            </template>
                                                            <template x-if="task.priority === 'medium'">
                                                                <div class="flex items-center space-x-1">
                                                                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                                                    <span>Média</span>
                                                                </div>
                                                            </template>
                                                            <template x-if="task.priority === 'low'">
                                                                <div class="flex items-center space-x-1">
                                                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                                    <span>Baixa</span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                        <template x-if="task.due_date">
                                                            <div class="flex items-center space-x-1">
                                                                <i class="fas fa-calendar-alt"></i>
                                                                <span x-text="task.due_date"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                    <div class="mt-3 flex items-center justify-between">
                                                        <template x-if="task.cliente">
                                                            <span class="inline-flex items-center text-xs text-white/60">
                                                                <i class="fas fa-user mr-1"></i>
                                                                <span x-text="task.cliente.name"></span>
                                                            </span>
                                                        </template>
                                                        <template x-if="task.user">
                                                            <div class="flex items-center">
                                                                <img class="h-6 w-6 rounded-full ring-2 ring-white/20" :src="task.user.avatar" :alt="task.user.name">
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                            <button 
                                                @click="$wire.dispatch('openTaskModal', { listId: list.id })"
                                                class="w-full p-2 text-white/60 hover:text-white hover:bg-white/10 rounded transition-colors text-sm text-left"
                                            >
                                                <i class="fas fa-plus mr-2"></i>Adicionar tarefa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
