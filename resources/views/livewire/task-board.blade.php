<div class="h-full">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <button 
                wire:click="$toggle('showTaskModal')"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
                <i class="fas fa-plus mr-2"></i>{{ __('Nova Tarefa') }}
            </button>
        </div>
    </div>

    <div id="board" class="flex space-x-4 overflow-x-auto pb-4">
        @foreach($lists as $list)
            <div class="flex-shrink-0 w-80" data-list-id="{{ $list->id }}">
                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100 text-sm mb-4">
                        {{ $list->name }}
                        <span class="text-gray-500 dark:text-gray-400">({{ $list->tasks->count() }})</span>
                    </h3>

                    <div class="task-list space-y-2" data-tasks-container>
                        @foreach($list->tasks as $task)
                            <div 
                                class="task-card bg-white dark:bg-gray-800 p-4 rounded shadow cursor-pointer hover:shadow-md transition-shadow duration-200 relative group"
                                wire:key="task-{{ $task->id }}"
                                wire:sortable.item="{{ $task->id }}"
                            >
                                <!-- Botão Excluir -->
                                <button 
                                    wire:click.stop="deleteTask({{ $task->id }})"
                                    onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')"
                                    class="absolute top-2 right-2 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Conteúdo do Card (clicável para editar) -->
                                <div wire:click="editTask({{ $task->id }})">
                                    <!-- Prioridade e Data -->
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            @switch($task->priority)
                                                @case('high')
                                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                                    <span class="text-xs text-red-500">Alta</span>
                                                    @break
                                                @case('medium')
                                                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                                    <span class="text-xs text-yellow-500">Média</span>
                                                    @break
                                                @default
                                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                    <span class="text-xs text-green-500">Baixa</span>
                                            @endswitch
                                        </div>
                                        @if($task->due_date)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $task->due_date->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Título -->
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $task->title }}</h4>

                                    <!-- Cliente e Responsável -->
                                    <div class="mt-2 flex items-center justify-between">
                                        @if($task->cliente)
                                            <span class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-user mr-1"></i>
                                                {{ $task->cliente->name }}
                                            </span>
                                        @endif

                                        @if($task->user)
                                            <div class="flex items-center">
                                                <img class="h-6 w-6 rounded-full" src="{{ $task->user->profile_photo_url }}" alt="{{ $task->user->name }}">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal de Tarefa -->
    <x-modal name="task-modal" wire:model="showTaskModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ $editingTask ? 'Editar Tarefa' : 'Nova Tarefa' }}
            </h2>

            <form wire:submit.prevent="{{ $editingTask ? 'updateTask' : 'createTask' }}">
                <div class="space-y-4">
                    <!-- Título -->
                    <div>
                        <x-input-label for="taskForm.title" value="Título" />
                        <x-text-input id="taskForm.title" type="text" wire:model="taskForm.title" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('taskForm.title')" class="mt-2" />
                    </div>

                    <!-- Descrição -->
                    <div>
                        <x-input-label for="taskForm.description" value="Descrição" />
                        <textarea
                            id="taskForm.description"
                            wire:model="taskForm.description"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                        ></textarea>
                    </div>

                    <!-- Lista -->
                    <div>
                        <x-input-label for="taskForm.task_list_id" value="Lista" />
                        <select
                            id="taskForm.task_list_id"
                            wire:model="taskForm.task_list_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            required
                        >
                            <option value="">Selecione uma lista</option>
                            @foreach($lists as $list)
                                <option value="{{ $list->id }}">{{ $list->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('taskForm.task_list_id')" class="mt-2" />
                    </div>

                    <!-- Cliente -->
                    <div>
                        <x-input-label for="taskForm.cliente_id" value="Cliente" />
                        <select
                            id="taskForm.cliente_id"
                            wire:model="taskForm.cliente_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                        >
                            <option value="">Selecione um cliente</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Responsável -->
                    <div>
                        <x-input-label for="taskForm.user_id" value="Responsável" />
                        <select
                            id="taskForm.user_id"
                            wire:model="taskForm.user_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                        >
                            <option value="">Selecione um responsável</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Prioridade -->
                    <div>
                        <x-input-label for="taskForm.priority" value="Prioridade" />
                        <select
                            id="taskForm.priority"
                            wire:model="taskForm.priority"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            required
                        >
                            <option value="low">Baixa</option>
                            <option value="medium">Média</option>
                            <option value="high">Alta</option>
                        </select>
                    </div>

                    <!-- Data de Vencimento -->
                    <div>
                        <x-input-label for="taskForm.due_date" value="Data de Vencimento" />
                        <x-text-input
                            id="taskForm.due_date"
                            type="date"
                            wire:model="taskForm.due_date"
                            class="mt-1 block w-full"
                        />
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <x-secondary-button wire:click="$set('showTaskModal', false)">
                        Cancelar
                    </x-secondary-button>
                    <x-primary-button type="submit">
                        {{ $editingTask ? 'Atualizar' : 'Criar' }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
