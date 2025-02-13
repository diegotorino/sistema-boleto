<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-secondary-dark overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                            <i class="fas fa-plus-circle mr-2"></i> Novo Boleto
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Preencha os dados abaixo para gerar um novo boleto
                        </p>
                    </div>

                    <form method="POST" action="{{ route('boletos.store') }}" class="space-y-6">
                        @csrf

                        <!-- Cliente -->
                        <div>
                            <x-input-label for="cliente_id" :value="__('Cliente')" />
                            <select id="cliente_id" name="cliente_id" class="form-select" required>
                                <option value="">Selecione um cliente</option>
                                @foreach(\App\Models\Cliente::orderBy('nome')->get() as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nome }} - {{ $cliente->cpf_cnpj }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                        </div>

                        <!-- Valor -->
                        <div>
                            <x-input-label for="valor" :value="__('Valor')" />
                            <x-text-input id="valor" class="block mt-1 w-full" type="number" name="valor" :value="old('valor')" required step="0.01" min="0" />
                            <x-input-error :messages="$errors->get('valor')" class="mt-2" />
                        </div>

                        <!-- Vencimento -->
                        <div>
                            <x-input-label for="vencimento" :value="__('Data de Vencimento')" />
                            <x-text-input id="vencimento" class="block mt-1 w-full" type="date" name="vencimento" :value="old('vencimento')" required />
                            <x-input-error :messages="$errors->get('vencimento')" class="mt-2" />
                        </div>

                        <!-- Descrição -->
                        <div>
                            <x-input-label for="descricao" :value="__('Descrição')" />
                            <textarea id="descricao" name="descricao" class="form-textarea" rows="3">{{ old('descricao') }}</textarea>
                            <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-file-invoice-dollar mr-2"></i>
                                {{ __('Gerar Boleto') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Máscaras para os campos
        document.addEventListener('DOMContentLoaded', function() {
            // Valor
            const valorInput = document.getElementById('valor');
            valorInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                value = (parseInt(value) / 100).toFixed(2);
                e.target.value = value;
            });
        });
    </script>
    @endpush
</x-app-layout>
