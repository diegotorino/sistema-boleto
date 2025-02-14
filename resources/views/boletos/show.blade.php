<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-secondary-dark overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                            <i class="fas fa-file-invoice-dollar mr-2"></i> Detalhes do Boleto
                        </h2>
                        <div class="flex space-x-3">
                            @if($boleto->pdf_path)
                                <a href="{{ route('boletos.pdf', $boleto) }}" 
                                   target="_blank"
                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                    <i class="fas fa-file-pdf mr-2"></i> Visualizar PDF
                                </a>
                            @endif
                            
                            <a href="{{ route('boletos.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i> Voltar
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informações do Boleto -->
                        <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                                <i class="fas fa-info-circle mr-2"></i> Informações do Boleto
                            </h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Número</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $boleto->seu_numero }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">R$ {{ number_format($boleto->valor_nominal, 2, ',', '.') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data de Vencimento</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($boleto->data_vencimento)->format('d/m/Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                    <dd class="mt-1">
                                        @switch($boleto->status)
                                            @case('EMITIDO')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Emitido
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
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Informações do Pagador -->
                        <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                                <i class="fas fa-user mr-2"></i> Informações do Pagador
                            </h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $boleto->pagador_nome }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $boleto->pagador_tipo === 'FISICA' ? 'Pessoa Física' : 'Pessoa Jurídica' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">CPF/CNPJ</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $boleto->pagador_cpf_cnpj }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">E-mail</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $boleto->pagador_email ?: 'Não informado' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Endereço do Pagador -->
                        <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg md:col-span-2">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                                <i class="fas fa-map-marker-alt mr-2"></i> Endereço do Pagador
                            </h3>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Logradouro</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $boleto->pagador_endereco }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Número</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $boleto->pagador_numero }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Complemento</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $boleto->pagador_complemento ?: 'Não informado' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bairro</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $boleto->pagador_bairro }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cidade</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $boleto->pagador_cidade }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">UF</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $boleto->pagador_uf }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">CEP</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $boleto->pagador_cep }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($boleto->status === 'EMITIDO')
                        <div class="mt-6 flex justify-end space-x-3">
                            <form action="{{ route('boletos.pagar', $boleto) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" 
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
                                        onclick="return confirm('Tem certeza que deseja simular o pagamento deste boleto?')">
                                    <i class="fas fa-money-bill-wave mr-2"></i> Simular Pagamento
                                </button>
                            </form>

                            <form action="{{ route('boletos.cancelar', $boleto) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
                                        onclick="return confirm('Tem certeza que deseja cancelar este boleto?')">
                                    <i class="fas fa-times-circle mr-2"></i> Cancelar Boleto
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
