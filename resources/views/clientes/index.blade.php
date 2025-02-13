<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-secondary-dark overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                            <i class="fas fa-users mr-2"></i> Clientes
                        </h2>
                        <a href="{{ route('clientes.create') }}" class="btn-primary">
                            <i class="fas fa-plus-circle mr-2"></i> Novo Cliente
                        </a>
                    </div>

                    <!-- Filtros -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-primary-dark rounded-lg">
                        <form action="{{ route('clientes.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="nome" value="Nome" />
                                <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full" value="{{ request('nome') }}" />
                            </div>
                            
                            <div>
                                <x-input-label for="cpf_cnpj" value="CPF/CNPJ" />
                                <x-text-input id="cpf_cnpj" name="cpf_cnpj" type="text" class="mt-1 block w-full" value="{{ request('cpf_cnpj') }}" />
                            </div>

                            <div>
                                <x-input-label for="email" value="Email" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ request('email') }}" />
                            </div>

                            <div class="md:col-span-3 flex justify-end space-x-2">
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-search mr-2"></i> Filtrar
                                </button>
                                <a href="{{ route('clientes.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                    <i class="fas fa-undo mr-2"></i> Limpar
                                </a>
                            </div>
                        </form>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Tabela -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-border-color">
                            <thead class="bg-gray-50 dark:bg-primary-dark">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        <i class="fas fa-user mr-2"></i>Nome
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        <i class="fas fa-envelope mr-2"></i>Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        <i class="fas fa-id-card mr-2"></i>CPF/CNPJ
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        <i class="fas fa-phone mr-2"></i>Telefone
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        <i class="fas fa-cog mr-2"></i>Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-secondary-dark divide-y divide-gray-200 dark:divide-border-color">
                                @forelse($clientes as $cliente)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-border-color transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $cliente->nome }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $cliente->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $cliente->cpf_cnpj }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $cliente->telefone }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('clientes.edit', $cliente) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-3">
                                                <i class="fas fa-edit mr-1"></i>Editar
                                            </a>
                                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600"
                                                        onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
                                                    <i class="fas fa-trash-alt mr-1"></i>Excluir
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-info-circle mr-2"></i>Nenhum cliente cadastrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $clientes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
