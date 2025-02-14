<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total de Boletos -->
                <div class="bg-white dark:bg-secondary-dark overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-10">
                                <i class="fas fa-file-invoice-dollar text-2xl text-accent-color"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de Boletos</p>
                                <p class="text-lg font-semibold text-gray-700 dark:text-white">{{ $totalBoletos ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boletos Pagos -->
                <div class="bg-white dark:bg-secondary-dark overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-10">
                                <i class="fas fa-check-circle text-2xl text-green-500"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Boletos Pagos</p>
                                <p class="text-lg font-semibold text-gray-700 dark:text-white">{{ $boletosPagos ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boletos Pendentes -->
                <div class="bg-white dark:bg-secondary-dark overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-500 bg-opacity-10">
                                <i class="fas fa-clock text-2xl text-yellow-500"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Boletos Pendentes</p>
                                <p class="text-lg font-semibold text-gray-700 dark:text-white">{{ $boletosPendentes ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimos Boletos -->
            <div class="bg-white dark:bg-secondary-dark overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-white mb-4">
                        <i class="fas fa-history mr-2"></i> Últimos Boletos
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-border-color">
                            <thead class="bg-gray-50 dark:bg-primary-dark">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">CPF/CNPJ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vencimento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-secondary-dark divide-y divide-gray-200 dark:divide-border-color">
                                @forelse($ultimosBoletos as $boleto)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-border-color transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ $boleto->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ $boleto->pagador_nome }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ $boleto->pagador_cpf_cnpj }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            R$ {{ number_format($boleto->valor_nominal, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ \Carbon\Carbon::parse($boleto->data_vencimento)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @switch($boleto->status)
                                                @case('EMITIDO')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pendente
                                                    </span>
                                                    @break
                                                @case('PAGO')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Pago
                                                    </span>
                                                    @break
                                                @case('CANCELADO')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Cancelado
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        {{ $boleto->status }}
                                                    </span>
                                            @endswitch
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <div class="flex justify-center items-center space-x-3">
                                                <!-- Visualizar informações -->
                                                <a href="{{ route('boletos.show', $boleto) }}" 
                                                   class="text-gray-600 hover:text-gray-900 transition-colors duration-200"
                                                   title="Visualizar Informações">
                                                    <i class="fas fa-eye text-lg"></i>
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
                                        <td colspan="7" class="py-4 px-6 text-center text-gray-500">
                                            Nenhum boleto encontrado
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
