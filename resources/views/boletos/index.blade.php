<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-secondary-dark overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                            <i class="fas fa-file-invoice-dollar mr-2"></i> Boletos
                        </h2>
                        <a href="{{ route('boletos.create') }}" class="btn-primary">
                            <i class="fas fa-plus-circle mr-2"></i> Novo Boleto
                        </a>
                    </div>

                    <!-- Filtros -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-primary-dark rounded-lg">
                        <form action="{{ route('boletos.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="cliente" value="Cliente" />
                                <x-text-input id="cliente" name="cliente" type="text" class="mt-1 block w-full" value="{{ request('cliente') }}" />
                            </div>
                            
                            <div>
                                <x-input-label for="status" value="Status" />
                                <select name="status" id="status" class="form-select mt-1 block w-full">
                                    <option value="">Todos</option>
                                    <option value="pago" {{ request('status') === 'pago' ? 'selected' : '' }}>Pago</option>
                                    <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="vencido" {{ request('status') === 'vencido' ? 'selected' : '' }}>Vencido</option>
                                    <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="data_inicio" value="Data Início" />
                                <x-text-input id="data_inicio" name="data_inicio" type="date" class="mt-1 block w-full" value="{{ request('data_inicio') }}" />
                            </div>

                            <div>
                                <x-input-label for="data_fim" value="Data Fim" />
                                <x-text-input id="data_fim" name="data_fim" type="date" class="mt-1 block w-full" value="{{ request('data_fim') }}" />
                            </div>

                            <div class="md:col-span-4 flex justify-end space-x-2">
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-search mr-2"></i> Filtrar
                                </button>
                                <a href="{{ route('boletos.index') }}" class="btn-secondary">
                                    <i class="fas fa-undo mr-2"></i> Limpar
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Tabela -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-border-color">
                            <thead class="bg-gray-50 dark:bg-primary-dark">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vencimento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-secondary-dark divide-y divide-gray-200 dark:divide-border-color">
                                @forelse($boletos as $boleto)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-border-color transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ $boleto->cliente->nome }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            R$ {{ number_format($boleto->valor, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ \Carbon\Carbon::parse($boleto->vencimento)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $boleto->status === 'pago' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $boleto->status === 'pendente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $boleto->status === 'vencido' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $boleto->status === 'cancelado' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ ucfirst($boleto->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="{{ route('boletos.show', $boleto) }}" class="btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('boletos.edit', $boleto) }}" class="btn-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($boleto->status !== 'pago' && $boleto->status !== 'cancelado')
                                                <form action="{{ route('boletos.destroy', $boleto) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-danger" onclick="return confirm('Tem certeza que deseja excluir este boleto?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            Nenhum boleto encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="mt-4">
                        {{ $boletos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
