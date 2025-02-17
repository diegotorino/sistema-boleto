<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-900 to-purple-900">
        <!-- Quadro de Tarefas -->
        <div class="p-6">
            <div class="flex space-x-6 overflow-x-auto pb-4 min-h-[calc(100vh-8rem)]">
                <!-- Início -->
                <div class="flex-shrink-0 w-80 bg-gray-100/10 rounded-xl backdrop-blur-sm">
                    <div class="p-3 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-white">
                            Início
                            <span class="ml-2 text-sm text-white/60">({{ count($tasks['inicio']) }})</span>
                        </h3>
                        <div class="flex items-center">
                            <button class="text-white/60 hover:text-white">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                        </div>
                    </div>
                    <div class="task-list px-2 pb-2" id="inicio" data-status="inicio">
                        @foreach($tasks['inicio'] as $task)
                            <div 
                                class="task-card bg-[#1a1a1a] hover:bg-[#242424] p-3 rounded-lg shadow-sm backdrop-blur-sm mb-3 cursor-move border border-white/5"
                                data-id="{{ $task->id }}"
                                onclick="showTaskDetails({{ $task->id }})"
                            >
                                <h4 class="font-medium text-white mb-2">{{ $task->title }}</h4>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-2">
                                        @switch($task->priority)
                                            @case('high')
                                                <span class="px-2 py-1 rounded-full text-xs bg-red-500/20 text-red-300 border border-red-500/20">Alta</span>
                                                @break
                                            @case('medium')
                                                <span class="px-2 py-1 rounded-full text-xs bg-yellow-500/20 text-yellow-300 border border-yellow-500/20">Média</span>
                                                @break
                                            @default
                                                <span class="px-2 py-1 rounded-full text-xs bg-green-500/20 text-green-300 border border-green-500/20">Baixa</span>
                                        @endswitch
                                    </div>
                                    @if($task->due_date)
                                        <span class="text-white/60">
                                            {{ $task->due_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                                @if($task->cliente)
                                    <div class="mt-2 flex items-center text-sm text-white/60">
                                        <img class="h-6 w-6 rounded-full mr-2" src="https://ui-avatars.com/api/?name={{ urlencode($task->cliente->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ $task->cliente->name }}">
                                        {{ $task->cliente->name }}
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <button onclick="window.location.href='{{ route('tasks.create') }}'" class="w-full p-2 text-white/60 hover:text-white hover:bg-[#1a1a1a] rounded-lg transition-colors text-sm text-left mt-2">
                            <i class="fas fa-plus mr-2"></i>Adicionar um cartão
                        </button>
                    </div>
                </div>

                <!-- Em Andamento -->
                <div class="flex-shrink-0 w-80 bg-gray-100/10 rounded-xl backdrop-blur-sm">
                    <div class="p-3 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-white">
                            Em Andamento
                            <span class="ml-2 text-sm text-white/60">({{ count($tasks['andamento']) }})</span>
                        </h3>
                        <div class="flex items-center">
                            <button class="text-white/60 hover:text-white">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                        </div>
                    </div>
                    <div class="task-list px-2 pb-2" id="andamento" data-status="andamento">
                        @foreach($tasks['andamento'] as $task)
                            <div 
                                class="task-card bg-[#1a1a1a] hover:bg-[#242424] p-3 rounded-lg shadow-sm backdrop-blur-sm mb-3 cursor-move border border-white/5"
                                data-id="{{ $task->id }}"
                                onclick="showTaskDetails({{ $task->id }})"
                            >
                                <h4 class="font-medium text-white mb-2">{{ $task->title }}</h4>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-2">
                                        @switch($task->priority)
                                            @case('high')
                                                <span class="px-2 py-1 rounded-full text-xs bg-red-500/20 text-red-300 border border-red-500/20">Alta</span>
                                                @break
                                            @case('medium')
                                                <span class="px-2 py-1 rounded-full text-xs bg-yellow-500/20 text-yellow-300 border border-yellow-500/20">Média</span>
                                                @break
                                            @default
                                                <span class="px-2 py-1 rounded-full text-xs bg-green-500/20 text-green-300 border border-green-500/20">Baixa</span>
                                        @endswitch
                                    </div>
                                    @if($task->due_date)
                                        <span class="text-white/60">
                                            {{ $task->due_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                                @if($task->cliente)
                                    <div class="mt-2 flex items-center text-sm text-white/60">
                                        <img class="h-6 w-6 rounded-full mr-2" src="https://ui-avatars.com/api/?name={{ urlencode($task->cliente->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ $task->cliente->name }}">
                                        {{ $task->cliente->name }}
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <button onclick="window.location.href='{{ route('tasks.create') }}'" class="w-full p-2 text-white/60 hover:text-white hover:bg-[#1a1a1a] rounded-lg transition-colors text-sm text-left mt-2">
                            <i class="fas fa-plus mr-2"></i>Adicionar um cartão
                        </button>
                    </div>
                </div>

                <!-- Concluído -->
                <div class="flex-shrink-0 w-80 bg-gray-100/10 rounded-xl backdrop-blur-sm">
                    <div class="p-3 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-white">
                            Concluído
                            <span class="ml-2 text-sm text-white/60">({{ count($tasks['concluido']) }})</span>
                        </h3>
                        <div class="flex items-center">
                            <button class="text-white/60 hover:text-white">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                        </div>
                    </div>
                    <div class="task-list px-2 pb-2" id="concluido" data-status="concluido">
                        @foreach($tasks['concluido'] as $task)
                            <div 
                                class="task-card bg-[#1a1a1a] hover:bg-[#242424] p-3 rounded-lg shadow-sm backdrop-blur-sm mb-3 cursor-move border border-white/5"
                                data-id="{{ $task->id }}"
                                onclick="showTaskDetails({{ $task->id }})"
                            >
                                <h4 class="font-medium text-white mb-2">{{ $task->title }}</h4>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-2">
                                        @switch($task->priority)
                                            @case('high')
                                                <span class="px-2 py-1 rounded-full text-xs bg-red-500/20 text-red-300 border border-red-500/20">Alta</span>
                                                @break
                                            @case('medium')
                                                <span class="px-2 py-1 rounded-full text-xs bg-yellow-500/20 text-yellow-300 border border-yellow-500/20">Média</span>
                                                @break
                                            @default
                                                <span class="px-2 py-1 rounded-full text-xs bg-green-500/20 text-green-300 border border-green-500/20">Baixa</span>
                                        @endswitch
                                    </div>
                                    @if($task->due_date)
                                        <span class="text-white/60">
                                            {{ $task->due_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                                @if($task->cliente)
                                    <div class="mt-2 flex items-center text-sm text-white/60">
                                        <img class="h-6 w-6 rounded-full mr-2" src="https://ui-avatars.com/api/?name={{ urlencode($task->cliente->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ $task->cliente->name }}">
                                        {{ $task->cliente->name }}
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <button onclick="window.location.href='{{ route('tasks.create') }}'" class="w-full p-2 text-white/60 hover:text-white hover:bg-[#1a1a1a] rounded-lg transition-colors text-sm text-left mt-2">
                            <i class="fas fa-plus mr-2"></i>Adicionar um cartão
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        window.addEventListener('load', function() {
            var lists = document.querySelectorAll('.task-list');
            
            lists.forEach(function(list) {
                new Sortable(list, {
                    group: 'board',
                    animation: 150,
                    ghostClass: 'opacity-50',
                    dragClass: 'cursor-grabbing',
                    onEnd: function(evt) {
                        var taskId = evt.item.getAttribute('data-id');
                        var newStatus = evt.to.getAttribute('data-status');
                        var newPosition = Array.from(evt.to.children).indexOf(evt.item);

                        fetch(`/tasks/${taskId}/status`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                status: newStatus,
                                position: newPosition
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            updateCounters();
                        });
                    }
                });
            });

            function updateCounters() {
                ['inicio', 'andamento', 'concluido'].forEach(function(status) {
                    var list = document.getElementById(status);
                    var counter = list.closest('.bg-gray-100\\/10').querySelector('span');
                    var count = Array.from(list.children).filter(child => child.classList.contains('task-card')).length;
                    counter.textContent = `(${count})`;
                });
            }
        });

        function showTaskDetails(taskId) {
            if (document.querySelector('.sortable-ghost')) return;

            fetch(`/tasks/${taskId}`)
                .then(response => response.json())
                .then(task => {
                    const statusText = task.status === 'inicio' ? 'Início' : 
                                     task.status === 'andamento' ? 'Em Andamento' : 'Concluído';

                    Swal.fire({
                        html: `
                            <div class="text-left">
                                <!-- Cabeçalho -->
                                <div class="flex items-center justify-between mb-6">
                                    <div class="flex-1">
                                        <input type="text" value="${task.title}" 
                                            style="background-color: #1a1a1a !important;"
                                            class="w-full text-xl font-bold border-0 text-white focus:ring-0 hover:bg-[#242424] rounded px-3 py-2 placeholder-gray-400"
                                            onchange="updateTaskField(${task.id}, 'title', this.value)"
                                            placeholder="Título da tarefa"
                                        >
                                    </div>
                                    <div class="text-white/60 text-sm ml-4">
                                        na lista <span class="underline cursor-pointer hover:text-white">${statusText}</span>
                                    </div>
                                </div>

                                <!-- Metadados -->
                                <div class="mb-6">
                                    <!-- Prioridade -->
                                    <div class="flex items-center space-x-3">
                                        <span class="text-white/60 min-w-20">Prioridade</span>
                                        <select 
                                            style="background-color: #1a1a1a !important;"
                                            class="flex-1 border-0 text-white rounded px-3 py-2 text-sm hover:bg-[#242424] focus:ring-0 placeholder-gray-400"
                                            onchange="updateTaskField(${task.id}, 'priority', this.value)"
                                        >
                                            <option value="low" ${task.priority === 'low' ? 'selected' : ''}>Baixa</option>
                                            <option value="medium" ${task.priority === 'medium' ? 'selected' : ''}>Média</option>
                                            <option value="high" ${task.priority === 'high' ? 'selected' : ''}>Alta</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Descrição -->
                                <div class="mb-6">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-align-left text-white/60 mr-2"></i>
                                        <span class="text-white/60">Descrição</span>
                                    </div>
                                    <textarea 
                                        style="background-color: #1a1a1a !important;"
                                        class="w-full border-0 text-white rounded p-3 min-h-[120px] resize-none hover:bg-[#242424] focus:ring-0 placeholder-gray-400"
                                        onchange="updateTaskField(${task.id}, 'description', this.value)"
                                        placeholder="Adicione uma descrição mais detalhada..."
                                    >${task.description || ''}</textarea>
                                </div>

                                <!-- Ações -->
                                <div class="flex items-center justify-between pt-4 border-t border-white/10">
                                    <button onclick="deleteTask(${task.id})" class="text-red-500 hover:text-red-400 flex items-center">
                                        <i class="fas fa-trash mr-2"></i> Excluir cartão
                                    </button>
                                </div>
                            </div>
                        `,
                        showCloseButton: true,
                        showConfirmButton: false,
                        width: '600px',
                        padding: '24px',
                        background: '#1a1a1a',
                        customClass: {
                            popup: 'border border-white/10',
                            closeButton: 'focus:outline-none hover:text-red-500 text-gray-400'
                        }
                    });
                });
        }

        function updateTaskField(taskId, field, value) {
            fetch(`/tasks/${taskId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    [field]: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const card = document.querySelector(`[data-id="${taskId}"]`);
                    if (card) {
                        if (field === 'title') {
                            card.querySelector('h4').textContent = value;
                        }
                        // Atualizar outros campos visuais conforme necessário
                    }
                }
            });
        }

        function deleteTask(taskId) {
            if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
                fetch(`/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.close();
                        const card = document.querySelector(`[data-id="${taskId}"]`);
                        if (card) {
                            card.remove();
                            updateCounters();
                        }
                    }
                });
            }
        }
    </script>
    @endpush
</x-app-layout>
