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
                            <div class="col-md-3">
                                <x-input-label for="seuNumero" :value="__('Seu Número')" />
                                <x-text-input id="seuNumero" class="block mt-1 w-full" type="text" name="seuNumero" :value="old('seuNumero')" required />
                                <x-input-error :messages="$errors->get('seuNumero')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <x-input-label for="valorNominal" :value="__('Valor (R$)')" />
                                <x-text-input id="valorNominal" class="block mt-1 w-full" type="number" name="valorNominal" :value="old('valorNominal')" required step="0.01" min="0.01" />
                                <x-input-error :messages="$errors->get('valorNominal')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <x-input-label for="dataVencimento" :value="__('Data de Vencimento')" />
                                <x-text-input id="dataVencimento" class="block mt-1 w-full" type="date" name="dataVencimento" :value="old('dataVencimento')" required />
                                <x-input-error :messages="$errors->get('dataVencimento')" class="mt-2" />
                            </div>

                            <div class="col-md-3">
                                <x-input-label for="numDiasAgenda" :value="__('Dias para Agenda')" />
                                <x-text-input id="numDiasAgenda" class="block mt-1 w-full" type="number" name="numDiasAgenda" :value="old('numDiasAgenda', 60)" required min="1" />
                                <x-input-error :messages="$errors->get('numDiasAgenda')" class="mt-2" />
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
                            <x-primary-button>
                                {{ __('Gerar Boleto') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.querySelector('#pagador\\[endereco\\]\\[cep\\]');
    const logradouroInput = document.querySelector('#pagador\\[endereco\\]\\[logradouro\\]');
    const bairroInput = document.querySelector('#pagador\\[endereco\\]\\[bairro\\]');
    const cidadeInput = document.querySelector('#pagador\\[endereco\\]\\[cidade\\]');
    const ufSelect = document.querySelector('#pagador\\[endereco\\]\\[uf\\]');

    cepInput.addEventListener('blur', async function() {
        const cep = this.value.replace(/\D/g, '');
        
        if (cep.length !== 8) {
            return;
        }

        try {
            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await response.json();

            if (!data.erro) {
                logradouroInput.value = data.logradouro;
                bairroInput.value = data.bairro;
                cidadeInput.value = data.localidade;
                ufSelect.value = data.uf;
            }
        } catch (error) {
            console.error('Erro ao buscar CEP:', error);
        }
    });

    const cpfCnpjInput = document.querySelector('#pagador\\[cpfCnpj\\]');
    const tipoPessoaSelect = document.querySelector('#pagador\\[tipoPessoa\\]');

    cpfCnpjInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        if (tipoPessoaSelect.value === 'FISICA') {
            if (value.length > 11) value = value.slice(0, 11);
            if (value.length > 9) {
                value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2}).*/, '$1.$2.$3-$4');
            } else if (value.length > 6) {
                value = value.replace(/^(\d{3})(\d{3})(\d{0,3}).*/, '$1.$2.$3');
            } else if (value.length > 3) {
                value = value.replace(/^(\d{3})(\d{0,3}).*/, '$1.$2');
            }
        } else if (tipoPessoaSelect.value === 'JURIDICA') {
            if (value.length > 14) value = value.slice(0, 14);
            if (value.length > 12) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2}).*/, '$1.$2.$3/$4-$5');
            } else if (value.length > 8) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4}).*/, '$1.$2.$3/$4');
            } else if (value.length > 5) {
                value = value.replace(/^(\d{2})(\d{3})(\d{0,3}).*/, '$1.$2.$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{0,3}).*/, '$1.$2');
            }
        }
        
        e.target.value = value;
    });

    tipoPessoaSelect.addEventListener('change', function() {
        cpfCnpjInput.value = '';
    });
});
</script>
@endpush
