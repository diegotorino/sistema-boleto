@extends('layouts.dashboard')

@section('header', 'Novo Boleto')

@section('content')
    <!-- Card Principal -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Ops!</strong>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('boletos.store') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Valor -->
                    <div>
                        <label for="valor" class="block text-sm font-medium text-gray-700">Valor</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">R$</span>
                            </div>
                            <input type="number" step="0.01" name="valor" id="valor" 
                                class="pl-8 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('valor') }}" required>
                        </div>
                    </div>

                    <!-- Vencimento -->
                    <div>
                        <label for="vencimento" class="block text-sm font-medium text-gray-700">Data de Vencimento</label>
                        <input type="date" name="vencimento" id="vencimento" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('vencimento') }}" required>
                    </div>

                    <!-- Nome do Pagador -->
                    <div>
                        <label for="pagador_nome" class="block text-sm font-medium text-gray-700">Nome do Pagador</label>
                        <input type="text" name="pagador_nome" id="pagador_nome" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('pagador_nome') }}" required>
                    </div>

                    <!-- CPF/CNPJ do Pagador -->
                    <div>
                        <label for="pagador_cpf_cnpj" class="block text-sm font-medium text-gray-700">CPF/CNPJ do Pagador</label>
                        <input type="text" name="pagador_cpf_cnpj" id="pagador_cpf_cnpj" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('pagador_cpf_cnpj') }}" required>
                    </div>

                    <!-- Endereço do Pagador -->
                    <div class="md:col-span-2">
                        <label for="pagador_endereco" class="block text-sm font-medium text-gray-700">Endereço do Pagador</label>
                        <input type="text" name="pagador_endereco" id="pagador_endereco" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('pagador_endereco') }}" required>
                    </div>

                    <!-- Cidade do Pagador -->
                    <div>
                        <label for="pagador_cidade" class="block text-sm font-medium text-gray-700">Cidade</label>
                        <input type="text" name="pagador_cidade" id="pagador_cidade" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('pagador_cidade') }}" required>
                    </div>

                    <!-- Estado do Pagador -->
                    <div>
                        <label for="pagador_estado" class="block text-sm font-medium text-gray-700">Estado</label>
                        <select name="pagador_estado" id="pagador_estado" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Selecione um estado</option>
                            @foreach(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                <option value="{{ $uf }}" {{ old('pagador_estado') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- CEP do Pagador -->
                    <div>
                        <label for="pagador_cep" class="block text-sm font-medium text-gray-700">CEP</label>
                        <input type="text" name="pagador_cep" id="pagador_cep" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('pagador_cep') }}" required>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('boletos.index') }}" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Cancelar
                    </a>
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Gerar Boleto
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cpfCnpjInput = document.getElementById('pagador_cpf_cnpj');
            const cepInput = document.getElementById('pagador_cep');
            
            // Máscara para CPF/CNPJ
            cpfCnpjInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                } else {
                    value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                    value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                    value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                    value = value.replace(/(\d{4})(\d)/, '$1-$2');
                }
                e.target.value = value;
            });

            // Máscara para CEP
            cepInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/^(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
            });
        });
    </script>
    @endpush
@endsection
