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
                            <strong class="font-bold">Ops!</strong>
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
                                <x-input-label for="seuNumero" :value="__('Seu Número')" />
                                <x-text-input id="seuNumero" class="block mt-1 w-full" type="text" name="seuNumero" :value="old('seuNumero')" required />
                                <x-input-error :messages="$errors->get('seuNumero')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <x-input-label for="valorNominal" :value="__('Valor (R$)')" />
                                <x-text-input id="valorNominal" class="block mt-1 w-full" type="number" name="valorNominal" :value="old('valorNominal')" required step="0.01" min="0.01" onchange="calculateTotal()" />
                                <x-input-error :messages="$errors->get('valorNominal')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <x-input-label for="dataVencimento" :value="__('Data de Vencimento')" />
                                <x-text-input id="dataVencimento" class="block mt-1 w-full" type="date" name="dataVencimento" :value="old('dataVencimento')" required onchange="calculateTotal()" />
                                <x-input-error :messages="$errors->get('dataVencimento')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <x-input-label for="numDiasAgenda" :value="__('Dias para Agenda')" />
                                <x-text-input id="numDiasAgenda" class="block mt-1 w-full" type="number" name="numDiasAgenda" :value="old('numDiasAgenda', 60)" required min="1" />
                                <x-input-error :messages="$errors->get('numDiasAgenda')" class="mt-2" />
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">
                                    <h5 class="font-semibold mb-2">Resumo do Boleto</h5>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Valor Principal:</p>
                                            <p id="valorPrincipal" class="font-medium">R$ 0,00</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Multa (2%):</p>
                                            <p id="valorMulta" class="font-medium">R$ 0,00</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Juros (1% ao mês):</p>
                                            <p id="valorJuros" class="font-medium">R$ 0,00</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Total com Juros/Multa:</p>
                                            <p id="valorTotal" class="font-medium text-lg text-primary-600">R$ 0,00</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4 class="mb-3">Dados do Pagador</h4>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <x-input-label for="pagador[nome]" :value="__('Nome')" />
                                <x-text-input id="pagador[nome]" class="block mt-1 w-full" type="text" name="pagador[nome]" :value="old('pagador.nome')" required />
                                <x-input-error :messages="$errors->get('pagador.nome')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <x-input-label for="pagador[tipoPessoa]" :value="__('Tipo de Pessoa')" />
                                <select id="pagador[tipoPessoa]" name="pagador[tipoPessoa]" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <option value="FISICA" {{ old('pagador.tipoPessoa') == 'FISICA' ? 'selected' : '' }}>Física</option>
                                    <option value="JURIDICA" {{ old('pagador.tipoPessoa') == 'JURIDICA' ? 'selected' : '' }}>Jurídica</option>
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

                            <div class="col-md-3">
                                <x-input-label for="pagador[endereco][cep]" :value="__('CEP')" />
                                <x-text-input id="pagador[endereco][cep]" class="block mt-1 w-full" type="text" name="pagador[endereco][cep]" :value="old('pagador.endereco.cep')" required />
                                <x-input-error :messages="$errors->get('pagador.endereco.cep')" class="mt-2" />
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <x-input-label for="pagador[endereco][logradouro]" :value="__('Logradouro')" />
                                <x-text-input id="pagador[endereco][logradouro]" class="block mt-1 w-full" type="text" name="pagador[endereco][logradouro]" :value="old('pagador.endereco.logradouro')" required />
                                <x-input-error :messages="$errors->get('pagador.endereco.logradouro')" class="mt-2" />
                            </div>

                            <div class="col-md-2">
                                <x-input-label for="pagador[endereco][numero]" :value="__('Número')" />
                                <x-text-input id="pagador[endereco][numero]" class="block mt-1 w-full" type="text" name="pagador[endereco][numero]" :value="old('pagador.endereco.numero')" required />
                                <x-input-error :messages="$errors->get('pagador.endereco.numero')" class="mt-2" />
                            </div>

                            <div class="col-md-4">
                                <x-input-label for="pagador[endereco][complemento]" :value="__('Complemento')" />
                                <x-text-input id="pagador[endereco][complemento]" class="block mt-1 w-full" type="text" name="pagador[endereco][complemento]" :value="old('pagador.endereco.complemento')" />
                                <x-input-error :messages="$errors->get('pagador.endereco.complemento')" class="mt-2" />
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <x-input-label for="pagador[endereco][bairro]" :value="__('Bairro')" />
                                <x-text-input id="pagador[endereco][bairro]" class="block mt-1 w-full" type="text" name="pagador[endereco][bairro]" :value="old('pagador.endereco.bairro')" required />
                                <x-input-error :messages="$errors->get('pagador.endereco.bairro')" class="mt-2" />
                            </div>

                            <div class="col-md-4">
                                <x-input-label for="pagador[endereco][cidade]" :value="__('Cidade')" />
                                <x-text-input id="pagador[endereco][cidade]" class="block mt-1 w-full" type="text" name="pagador[endereco][cidade]" :value="old('pagador.endereco.cidade')" required />
                                <x-input-error :messages="$errors->get('pagador.endereco.cidade')" class="mt-2" />
                            </div>

                            <div class="col-md-4">
                                <x-input-label for="pagador[endereco][uf]" :value="__('UF')" />
                                <select id="pagador[endereco][uf]" name="pagador[endereco][uf]" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    @foreach(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                        <option value="{{ $uf }}" {{ old('pagador.endereco.uf') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('pagador.endereco.uf')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="btn-primary" id="submitButton">
                                <i class="fas fa-save mr-2"></i> Gerar Boleto
                            </button>
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
                ]
            });

            IMask(document.querySelector('#pagador\\[endereco\\]\\[cep\\]'), {
                mask: '00000-000'
            });

            // Cliente seleção
            const clienteSelect = document.querySelector('#cliente_id');
            clienteSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                if (option.value) {
                    const cliente = JSON.parse(option.dataset.cliente);
                    preencherDadosCliente(cliente);
                } else {
                    limparDadosCliente();
                }
            });

            // CEP autopreenchimento
            const cepInput = document.querySelector('#pagador\\[endereco\\]\\[cep\\]');
            cepInput.addEventListener('blur', async function() {
                const cep = this.value.replace(/\D/g, '');
                if (cep.length === 8) {
                    try {
                        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                        const data = await response.json();
                        if (!data.erro) {
                            document.querySelector('#pagador\\[endereco\\]\\[logradouro\\]').value = data.logradouro;
                            document.querySelector('#pagador\\[endereco\\]\\[bairro\\]').value = data.bairro;
                            document.querySelector('#pagador\\[endereco\\]\\[cidade\\]').value = data.localidade;
                            document.querySelector('#pagador\\[endereco\\]\\[uf\\]').value = data.uf;
                        }
                    } catch (error) {
                        console.error('Erro ao buscar CEP:', error);
                    }
                }
            });

            // Form submit
            const form = document.querySelector('form');
            const submitButton = document.querySelector('#submitButton');
            
            form.addEventListener('submit', function() {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Gerando...';
            });
        });

        function preencherDadosCliente(cliente) {
            document.querySelector('#pagador\\[nome\\]').value = cliente.nome;
            document.querySelector('#pagador\\[cpfCnpj\\]').value = cliente.cpf_cnpj;
            document.querySelector('#pagador\\[email\\]').value = cliente.email || '';
            document.querySelector('#pagador\\[tipoPessoa\\]').value = cliente.cpf_cnpj.length <= 11 ? 'FISICA' : 'JURIDICA';
            
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

        function limparDadosCliente() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => {
                if (input.name.startsWith('pagador[')) {
                    input.value = '';
                }
            });
            document.querySelector('#pagador\\[tipoPessoa\\]').value = '';
        }

        function calculateTotal() {
            const valor = parseFloat(document.querySelector('#valorNominal').value) || 0;
            const dataVencimento = new Date(document.querySelector('#dataVencimento').value);
            const hoje = new Date();
            
            document.querySelector('#valorPrincipal').textContent = `R$ ${valor.toFixed(2)}`;
            
            // Se a data de vencimento já passou
            if (dataVencimento < hoje) {
                const diasAtraso = Math.floor((hoje - dataVencimento) / (1000 * 60 * 60 * 24));
                const multa = valor * 0.02; // 2% de multa
                const juros = valor * (0.01 * (diasAtraso / 30)); // 1% ao mês pro rata
                
                document.querySelector('#valorMulta').textContent = `R$ ${multa.toFixed(2)}`;
                document.querySelector('#valorJuros').textContent = `R$ ${juros.toFixed(2)}`;
                document.querySelector('#valorTotal').textContent = `R$ ${(valor + multa + juros).toFixed(2)}`;
            } else {
                document.querySelector('#valorMulta').textContent = 'R$ 0,00';
                document.querySelector('#valorJuros').textContent = 'R$ 0,00';
                document.querySelector('#valorTotal').textContent = `R$ ${valor.toFixed(2)}`;
            }
        }
    </script>
    @endpush
</x-app-layout>
