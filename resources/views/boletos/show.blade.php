@extends('layouts.dashboard')

@section('header', 'Detalhes do Boleto')

@section('content')
    <!-- Card Principal -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6">
            <!-- Status do Boleto -->
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $boleto->status === 'pago' ? 'bg-green-100 text-green-800' : 
                           ($boleto->status === 'vencido' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($boleto->status) }}
                    </span>
                </div>
                <div class="flex space-x-3">
                    <a href="#" onclick="window.open('{{ route('boletos.pdf', $boleto) }}', '_blank')" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Baixar PDF
                    </a>
                    <a href="{{ route('boletos.index') }}" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Informações do Boleto -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informações Financeiras -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Financeiras</h3>
                    <dl class="grid grid-cols-1 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Valor</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">R$ {{ number_format($boleto->valor, 2, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data de Vencimento</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $boleto->vencimento->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data de Emissão</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $boleto->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Linha Digitável</dt>
                            <dd class="mt-1 text-sm font-mono text-gray-900 break-all">{{ $boleto->linha_digitavel }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Informações do Pagador -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Pagador</h3>
                    <dl class="grid grid-cols-1 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nome</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $boleto->pagador_nome }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">CPF/CNPJ</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $boleto->pagador_cpf_cnpj }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Endereço</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $boleto->pagador_endereco }}<br>
                                {{ $boleto->pagador_cidade }}/{{ $boleto->pagador_estado }}<br>
                                CEP: {{ $boleto->pagador_cep }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Histórico -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Histórico</h3>
                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evento</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalhes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($boleto->historico as $evento)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $evento->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $evento->tipo }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $evento->detalhes }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        Nenhum evento registrado
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
