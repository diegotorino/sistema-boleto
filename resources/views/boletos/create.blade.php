<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-secondary-dark overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                            <i class="fas fa-plus-circle mr-2"></i> Gerar Novo Boleto
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Preencha os dados abaixo para gerar um novo boleto
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Erro!</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('boletos.store') }}" class="space-y-6">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <x-input-label for="cliente_id" :value="__('Selecione um Cliente')" />
                                <select id="cliente_id" name="cliente_id" class="form-select block mt-1 w-full">
                                    <option value="">Selecione um cliente...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" data-cliente="{{ json_encode([
                                            'nome' => $cliente->nome,
                                            'cpf_cnpj' => $cliente->cpf_cnpj,
                                            'email' => $cliente->email,
                                            'endereco' => [
                                                'logradouro' => $cliente->endereco,
                                                'numero' => $cliente->numero,
                                                'complemento' => $cliente->complemento,
                                                'bairro' => $cliente->bairro,
                                                'cidade' => $cliente->cidade,
                                                'uf' => $cliente->uf,
                                                'cep' => $cliente->cep
                                            ]
                                        ]) }}">
                                            {{ $cliente->nome }} - {{ $cliente->cpf_cnpj }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <x-input-label for="seuNumero" :value="__('Número do Boleto')" />
                                <x-text-input id="seuNumero" class="block mt-1 w-full" type="text" name="seuNumero" :value="old('seuNumero')" required />
                                <x-input-error :messages="$errors->get('seuNumero')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <x-input-label for="valorNominal" :value="__('Valor (R$)')" />
                                <x-text-input id="valorNominal" class="block mt-1 w-full" type="number" name="valorNominal" :value="old('valorNominal')" required step="0.01" min="0.01" onchange="calcularTotal()" />
                                <x-input-error :messages="$errors->get('valorNominal')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <x-input-label for="dataVencimento" :value="__('Data de Vencimento')" />
                                <x-text-input id="dataVencimento" class="block mt-1 w-full" type="date" name="dataVencimento" :value="old('dataVencimento')" required onchange="calcularTotal()" />
                                <x-input-error :messages="$errors->get('dataVencimento')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <x-input-label for="numDiasAgenda" :value="__('Dias para Agendar')" />
                                <x-text-input id="numDiasAgenda" class="block mt-1 w-full" type="number" name="numDiasAgenda" :value="old('numDiasAgenda', 1)" required min="1" />
                                <x-input-error :messages="$errors->get('numDiasAgenda')" class="mt-2" />
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <x-input-label for="multa" :value="__('Multa (%)')" />
                                <x-text-input id="multa" class="block mt-1 w-full" type="number" name="multa" :value="old('multa', 0)" required step="0.01" min="0" max="100" onchange="calcularTotal()" />
                                <x-input-error :messages="$errors->get('multa')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <x-input-label for="juros" :value="__('Juros ao Mês (%)')" />
                                <x-text-input id="juros" class="block mt-1 w-full" type="number" name="juros" :value="old('juros', 0)" required step="0.01" min="0" max="100" onchange="calcularTotal()" />
                                <x-input-error :messages="$errors->get('juros')" class="mt-2" />
                            </div>

                            <div class="col-md-6">
                                <x-input-label for="total" :value="__('Total com Multa e Juros')" />
                                <x-text-input id="total" class="block mt-1 w-full" type="text" readonly />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('boletos.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">
                                Cancelar
                            </a>
                            <x-primary-button>
                                Gerar Boleto
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/imask"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Máscaras de input
            IMask(document.querySelector('#pagador\\[cpfCnpj\\]'), {
                mask: [
                    { mask: '000.000.000-00', maxLength: 11 },
                    { mask: '00.000.000/0000-00', maxLength: 14 }
                ],
                dispatch: function (appended, dynamicMasked) {
                    const number = (dynamicMasked.value + appended).replace(/\D/g,'');
                    return dynamicMasked.compiledMasks.find(m => number.length <= m.maxLength);
                }
            });

            const telefoneInput = document.querySelector('#pagador\\[telefone\\]');
            if (telefoneInput) {
                IMask(telefoneInput, {
                    mask: [
                        { mask: '(00) 0000-0000' },
                        { mask: '(00) 00000-0000' }
                    ],
                    dispatch: function (appended, dynamicMasked) {
                        const number = (dynamicMasked.value + appended).replace(/\D/g,'');
                        return number.length <= 10 ? dynamicMasked.compiledMasks[0] : dynamicMasked.compiledMasks[1];
                    }
                });
            }

            const cepInput = document.querySelector('#pagador\\[endereco\\]\\[cep\\]');
            if (cepInput) {
                IMask(cepInput, {
                    mask: '00000-000'
                });
            }

            // Cliente selecionado
            const clienteSelect = document.querySelector('#cliente_id');
            if (clienteSelect) {
                clienteSelect.addEventListener('change', function() {
                    const option = this.options[this.selectedIndex];
                    if (option && option.dataset.cliente) {
                        const cliente = JSON.parse(option.dataset.cliente);
                        preencherDadosCliente(cliente);
                    }
                });
            }
        });

        function preencherDadosCliente(cliente) {
            document.querySelector('#pagador\\[nome\\]').value = cliente.nome;
            document.querySelector('#pagador\\[cpfCnpj\\]').value = cliente.cpf_cnpj;
            document.querySelector('#pagador\\[email\\]').value = cliente.email || '';
            
            if (cliente.endereco) {
                document.querySelector('#pagador\\[endereco\\]\\[cep\\]').value = cliente.endereco.cep;
                document.querySelector('#pagador\\[endereco\\]\\[logradouro\\]').value = cliente.endereco.logradouro;
                document.querySelector('#pagador\\[endereco\\]\\[numero\\]').value = cliente.endereco.numero;
                document.querySelector('#pagador\\[endereco\\]\\[complemento\\]').value = cliente.endereco.complemento || '';
                document.querySelector('#pagador\\[endereco\\]\\[bairro\\]').value = cliente.endereco.bairro;
                document.querySelector('#pagador\\[endereco\\]\\[cidade\\]').value = cliente.endereco.cidade;
                document.querySelector('#pagador\\[endereco\\]\\[uf\\]').value = cliente.endereco.uf;
            }
        }

        function calcularTotal() {
            const valor = parseFloat(document.querySelector('#valorNominal').value) || 0;
            const dataVencimento = new Date(document.querySelector('#dataVencimento').value);
            const hoje = new Date();
            
            if (isNaN(valor) || !dataVencimento || dataVencimento >= hoje) {
                document.querySelector('#total').value = valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                return;
            }

            const multa = parseFloat(document.querySelector('#multa').value) || 0;
            const juros = parseFloat(document.querySelector('#juros').value) || 0;
            
            const diasAtraso = Math.floor((hoje - dataVencimento) / (1000 * 60 * 60 * 24));
            const valorMulta = valor * (multa / 100);
            const valorJuros = valor * ((juros / 30) / 100) * diasAtraso;
            
            const total = valor + valorMulta + valorJuros;
            document.querySelector('#total').value = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }
    </script>
    @endpush
</x-app-layout>
