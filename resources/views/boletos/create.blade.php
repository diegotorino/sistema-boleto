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

                        <!-- Seleção de Cliente -->
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
                                            'telefone' => $cliente->telefone,
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

                        <!-- Dados do Boleto -->
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
                                <x-text-input id="numDiasAgenda" class="block mt-1 w-full" type="number" name="numDiasAgenda" :value="old('numDiasAgenda', 0)" required min="0" />
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

                        <!-- Dados do Pagador -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Dados do Pagador</h3>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <x-input-label for="pagador[nome]" :value="__('Nome do Pagador')" />
                                    <x-text-input id="pagador[nome]" class="block mt-1 w-full" type="text" name="pagador[nome]" :value="old('pagador.nome')" required />
                                    <x-input-error :messages="$errors->get('pagador.nome')" class="mt-2" />
                                </div>

                                <div class="col-md-3">
                                    <x-input-label for="pagador[tipoPessoa]" :value="__('Tipo de Pessoa')" />
                                    <select id="pagador[tipoPessoa]" name="pagador[tipoPessoa]" class="form-select block mt-1 w-full" required>
                                        <option value="FISICA">Pessoa Física</option>
                                        <option value="JURIDICA">Pessoa Jurídica</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('pagador.tipoPessoa')" class="mt-2" />
                                </div>

                                <div class="col-md-3">
                                    <x-input-label for="pagador[cpfCnpj]" :value="__('CPF/CNPJ')" />
                                    <x-text-input id="pagador[cpfCnpj]" class="block mt-1 w-full" type="text" name="pagador[cpfCnpj]" :value="old('pagador.cpfCnpj')" required />
                                    <x-input-error :messages="$errors->get('pagador.cpfCnpj')" class="mt-2" />
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <x-input-label for="pagador[email]" :value="__('E-mail')" />
                                    <x-text-input id="pagador[email]" class="block mt-1 w-full" type="email" name="pagador[email]" :value="old('pagador.email')" />
                                    <x-input-error :messages="$errors->get('pagador.email')" class="mt-2" />
                                </div>

                                <div class="col-md-6">
                                    <x-input-label for="pagador[telefone]" :value="__('Telefone')" />
                                    <x-text-input id="pagador[telefone]" class="block mt-1 w-full" type="text" name="pagador[telefone]" :value="old('pagador.telefone')" />
                                    <x-input-error :messages="$errors->get('pagador.telefone')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Endereço do Pagador -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Endereço do Pagador</h3>
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <x-input-label for="pagador[endereco][cep]" :value="__('CEP')" />
                                    <x-text-input id="pagador[endereco][cep]" class="block mt-1 w-full" type="text" name="pagador[endereco][cep]" :value="old('pagador.endereco.cep')" required />
                                    <x-input-error :messages="$errors->get('pagador.endereco.cep')" class="mt-2" />
                                </div>

                                <div class="col-md-7">
                                    <x-input-label for="pagador[endereco][logradouro]" :value="__('Logradouro')" />
                                    <x-text-input id="pagador[endereco][logradouro]" class="block mt-1 w-full" type="text" name="pagador[endereco][logradouro]" :value="old('pagador.endereco.logradouro')" required />
                                    <x-input-error :messages="$errors->get('pagador.endereco.logradouro')" class="mt-2" />
                                </div>

                                <div class="col-md-2">
                                    <x-input-label for="pagador[endereco][numero]" :value="__('Número')" />
                                    <x-text-input id="pagador[endereco][numero]" class="block mt-1 w-full" type="text" name="pagador[endereco][numero]" :value="old('pagador.endereco.numero')" required />
                                    <x-input-error :messages="$errors->get('pagador.endereco.numero')" class="mt-2" />
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <x-input-label for="pagador[endereco][complemento]" :value="__('Complemento')" />
                                    <x-text-input id="pagador[endereco][complemento]" class="block mt-1 w-full" type="text" name="pagador[endereco][complemento]" :value="old('pagador.endereco.complemento')" />
                                    <x-input-error :messages="$errors->get('pagador.endereco.complemento')" class="mt-2" />
                                </div>

                                <div class="col-md-4">
                                    <x-input-label for="pagador[endereco][bairro]" :value="__('Bairro')" />
                                    <x-text-input id="pagador[endereco][bairro]" class="block mt-1 w-full" type="text" name="pagador[endereco][bairro]" :value="old('pagador.endereco.bairro')" required />
                                    <x-input-error :messages="$errors->get('pagador.endereco.bairro')" class="mt-2" />
                                </div>

                                <div class="col-md-3">
                                    <x-input-label for="pagador[endereco][cidade]" :value="__('Cidade')" />
                                    <x-text-input id="pagador[endereco][cidade]" class="block mt-1 w-full" type="text" name="pagador[endereco][cidade]" :value="old('pagador.endereco.cidade')" required />
                                    <x-input-error :messages="$errors->get('pagador.endereco.cidade')" class="mt-2" />
                                </div>

                                <div class="col-md-1">
                                    <x-input-label for="pagador[endereco][uf]" :value="__('UF')" />
                                    <x-text-input id="pagador[endereco][uf]" class="block mt-1 w-full" type="text" name="pagador[endereco][uf]" :value="old('pagador.endereco.uf')" required maxlength="2" style="text-transform: uppercase;" />
                                    <x-input-error :messages="$errors->get('pagador.endereco.uf')" class="mt-2" />
                                </div>
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

            IMask(document.querySelector('#pagador\\[telefone\\]'), {
                mask: [
                    { mask: '(00) 0000-0000' },
                    { mask: '(00) 00000-0000' }
                ],
                dispatch: function (appended, dynamicMasked) {
                    const number = (dynamicMasked.value + appended).replace(/\D/g,'');
                    return number.length <= 10 ? dynamicMasked.compiledMasks[0] : dynamicMasked.compiledMasks[1];
                }
            });

            IMask(document.querySelector('#pagador\\[endereco\\]\\[cep\\]'), {
                mask: '00000-000'
            });

            // Cliente selecionado
            const clienteSelect = document.querySelector('#cliente_id');
            clienteSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                if (option && option.dataset.cliente) {
                    const cliente = JSON.parse(option.dataset.cliente);
                    preencherDadosCliente(cliente);
                }
            });
        });

        function preencherDadosCliente(cliente) {
            // Preenche dados básicos do pagador
            document.querySelector('#pagador\\[nome\\]').value = cliente.nome;
            document.querySelector('#pagador\\[cpfCnpj\\]').value = cliente.cpf_cnpj;
            document.querySelector('#pagador\\[email\\]').value = cliente.email || '';
            document.querySelector('#pagador\\[telefone\\]').value = cliente.telefone || '';
            
            // Determina o tipo de pessoa baseado no CPF/CNPJ
            const cpfCnpj = cliente.cpf_cnpj.replace(/\D/g, '');
            document.querySelector('#pagador\\[tipoPessoa\\]').value = cpfCnpj.length > 11 ? 'JURIDICA' : 'FISICA';
            
            // Preenche o endereço
            document.querySelector('#pagador\\[endereco\\]\\[cep\\]').value = cliente.endereco.cep;
            document.querySelector('#pagador\\[endereco\\]\\[logradouro\\]').value = cliente.endereco.logradouro;
            document.querySelector('#pagador\\[endereco\\]\\[numero\\]').value = cliente.endereco.numero;
            document.querySelector('#pagador\\[endereco\\]\\[complemento\\]').value = cliente.endereco.complemento || '';
            document.querySelector('#pagador\\[endereco\\]\\[bairro\\]').value = cliente.endereco.bairro;
            document.querySelector('#pagador\\[endereco\\]\\[cidade\\]').value = cliente.endereco.cidade;
            document.querySelector('#pagador\\[endereco\\]\\[uf\\]').value = cliente.endereco.uf.toUpperCase();
        }

        function calcularTotal() {
            const valor = parseFloat(document.getElementById('valorNominal').value) || 0;
            const multa = parseFloat(document.getElementById('multa').value) || 0;
            const juros = parseFloat(document.getElementById('juros').value) || 0;
            const dataVencimento = new Date(document.getElementById('dataVencimento').value);
            const hoje = new Date();

            let total = valor;
            
            if (dataVencimento < hoje) {
                // Adiciona multa
                total += (valor * (multa / 100));
                
                // Calcula dias de atraso
                const diffTime = Math.abs(hoje - dataVencimento);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                // Adiciona juros (proporcional aos dias de atraso)
                const jurosDiario = (juros / 30) / 100;
                total += (valor * jurosDiario * diffDays);
            }

            document.getElementById('total').value = total.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        }
    </script>
    @endpush
</x-app-layout>
