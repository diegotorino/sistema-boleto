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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Seu Número</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vencimento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pagador</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-secondary-dark divide-y divide-gray-200 dark:divide-border-color">
                                @forelse($boletos as $boleto)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-border-color transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ $boleto->seu_numero }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            R$ {{ number_format($boleto->valor_nominal, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ \Carbon\Carbon::parse($boleto->data_vencimento)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'EMITIDO' => 'warning',
                                                    'PAGO' => 'success',
                                                    'CANCELADO' => 'danger',
                                                    'VENCIDO' => 'secondary'
                                                ];
                                                $statusColor = $statusColors[$boleto->status] ?? 'primary';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $statusColor }}">
                                                {{ $boleto->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ $boleto->pagador_nome }}
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <div class="flex justify-center items-center space-x-3">
                                                <!-- Visualizar informações -->
                                                <a href="{{ route('boletos.show', $boleto) }}" 
                                                   class="text-gray-600 hover:text-gray-900 transition-colors duration-200"
                                                   title="Visualizar Informações">
                                                    <i class="fas fa-info-circle text-lg"></i>
                                                </a>

                                                <!-- PDF -->
                                                @if($boleto->pdf_path)
                                                    <a href="{{ route('boletos.pdf', $boleto) }}" 
                                                       target="_blank" 
                                                       class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                                       title="Visualizar PDF">
                                                        <i class="fas fa-file-pdf text-lg"></i>
                                                    </a>
                                                @endif

                                                <!-- Pagar -->
                                                @if($boleto->status === 'EMITIDO')
                                                    <form action="{{ route('boletos.pagar', $boleto) }}" 
                                                          method="POST" 
                                                          class="inline-block">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                                                title="Simular Pagamento">
                                                            <i class="fas fa-money-bill-wave text-lg"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Cancelar -->
                                                    <form action="{{ route('boletos.cancelar', $boleto) }}" 
                                                          method="POST" 
                                                          class="inline-block"
                                                          onsubmit="return confirm('Tem certeza que deseja cancelar este boleto?');">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                                title="Cancelar Boleto">
                                                            <i class="fas fa-times-circle text-lg"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
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
