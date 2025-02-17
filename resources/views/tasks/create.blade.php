<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#2d2d2d] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h2 class="text-2xl font-semibold text-white">
                            Nova Tarefa
                        </h2>
                    </div>

                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <!-- Título -->
                            <div>
                                <x-input-label for="title" value="Título" class="text-white" />
                                <input 
                                    type="text"
                                    id="title"
                                    name="title"
                                    style="background-color: #1a1a1a !important"
                                    class="mt-1 block w-full border-0 text-white focus:ring-0 hover:bg-[#242424] rounded-lg px-3 py-2 placeholder-gray-400"
                                    required
                                    placeholder="Digite o título da tarefa"
                                >
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <!-- Descrição -->
                            <div>
                                <x-input-label for="description" value="Descrição" class="text-white" />
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="3"
                                    style="background-color: #1a1a1a !important"
                                    class="mt-1 block w-full border-0 text-white focus:ring-0 hover:bg-[#242424] rounded-lg px-3 py-2 placeholder-gray-400"
                                    placeholder="Adicione uma descrição mais detalhada..."
                                ></textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <!-- Prioridade -->
                            <div>
                                <x-input-label for="priority" value="Prioridade" class="text-white" />
                                <select
                                    id="priority"
                                    name="priority"
                                    style="background-color: #1a1a1a !important"
                                    class="mt-1 block w-full border-0 text-white focus:ring-0 hover:bg-[#242424] rounded-lg px-3 py-2"
                                    required
                                >
                                    <option value="low">Baixa</option>
                                    <option value="medium" selected>Média</option>
                                    <option value="high">Alta</option>
                                </select>
                                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button 
                                    type="button" 
                                    onclick="window.history.back()" 
                                    class="px-4 py-2 text-white hover:bg-[#242424] rounded-lg transition-colors"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-accent-color text-white rounded-lg hover:bg-accent-color-dark transition-colors"
                                >
                                    Criar Tarefa
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
