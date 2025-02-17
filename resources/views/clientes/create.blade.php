<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <i class="fas fa-user-plus mr-2"></i>{{ __('Novo Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <p class="text-gray-600 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-2"></i>Preencha os dados do cliente abaixo
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

                    <form method="POST" action="{{ route('clientes.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nome -->
                            <div>
                                <x-input-label for="nome" :value="__('Nome')" />
                                <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome')" required autofocus />
                                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                            </div>

                            <!-- CPF/CNPJ -->
                            <div>
                                <x-input-label for="cpf_cnpj" :value="__('CPF/CNPJ')" />
                                <x-text-input id="cpf_cnpj" class="block mt-1 w-full" type="text" name="cpf_cnpj" :value="old('cpf_cnpj')" required />
                                <x-input-error :messages="$errors->get('cpf_cnpj')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Telefone -->
                            <div>
                                <x-input-label for="telefone" :value="__('Telefone')" />
                                <x-text-input id="telefone" class="block mt-1 w-full" type="text" name="telefone" :value="old('telefone')" />
                                <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                            </div>
                        </div>

                        <h4 class="font-semibold text-lg mt-6 mb-4">Endereço</h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- CEP -->
                            <div>
                                <x-input-label for="cep" :value="__('CEP')" />
                                <x-text-input id="cep" class="block mt-1 w-full" type="text" name="cep" :value="old('cep')" required />
                                <x-input-error :messages="$errors->get('cep')" class="mt-2" />
                            </div>

                            <!-- Logradouro -->
                            <div class="md:col-span-2">
                                <x-input-label for="endereco" :value="__('Logradouro')" />
                                <x-text-input id="endereco" class="block mt-1 w-full" type="text" name="endereco" :value="old('endereco')" required />
                                <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
                            </div>

                            <!-- Número -->
                            <div>
                                <x-input-label for="numero" :value="__('Número')" />
                                <x-text-input id="numero" class="block mt-1 w-full" type="text" name="numero" :value="old('numero')" required />
                                <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                            </div>

                            <!-- Complemento -->
                            <div>
                                <x-input-label for="complemento" :value="__('Complemento')" />
                                <x-text-input id="complemento" class="block mt-1 w-full" type="text" name="complemento" :value="old('complemento')" />
                                <x-input-error :messages="$errors->get('complemento')" class="mt-2" />
                            </div>

                            <!-- Bairro -->
                            <div>
                                <x-input-label for="bairro" :value="__('Bairro')" />
                                <x-text-input id="bairro" class="block mt-1 w-full" type="text" name="bairro" :value="old('bairro')" required />
                                <x-input-error :messages="$errors->get('bairro')" class="mt-2" />
                            </div>

                            <!-- Cidade -->
                            <div>
                                <x-input-label for="cidade" :value="__('Cidade')" />
                                <x-text-input id="cidade" class="block mt-1 w-full" type="text" name="cidade" :value="old('cidade')" required />
                                <x-input-error :messages="$errors->get('cidade')" class="mt-2" />
                            </div>

                            <!-- UF -->
                            <div>
                                <x-input-label for="uf" :value="__('UF')" />
                                <select id="uf" name="uf" class="form-select block mt-1 w-full" required>
                                    <option value="">Selecione...</option>
                                    @foreach(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                        <option value="{{ $uf }}" {{ old('uf') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('uf')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('clientes.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-3">
                                <i class="fas fa-arrow-left mr-2"></i>Voltar
                            </a>
                            <x-primary-button class="ml-4">
                                <i class="fas fa-save mr-2"></i>{{ __('Salvar') }}
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
            // Máscara para CPF/CNPJ
            IMask(document.getElementById('cpf_cnpj'), {
                mask: [
                    { mask: '000.000.000-00', maxLength: 11 },
                    { mask: '00.000.000/0000-00', maxLength: 14 }
                ]
            });

            // Máscara para telefone
            IMask(document.getElementById('telefone'), {
                mask: '(00) 00000-0000'
            });

            // Máscara para CEP
            IMask(document.getElementById('cep'), {
                mask: '00000-000'
            });

            // Busca de CEP
            const cepInput = document.getElementById('cep');
            cepInput.addEventListener('blur', async function() {
                const cep = this.value.replace(/\D/g, '');
                if (cep.length === 8) {
                    try {
                        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                        const data = await response.json();
                        if (!data.erro) {
                            document.getElementById('endereco').value = data.logradouro;
                            document.getElementById('bairro').value = data.bairro;
                            document.getElementById('cidade').value = data.localidade;
                            document.getElementById('uf').value = data.uf;
                        }
                    } catch (error) {
                        console.error('Erro ao buscar CEP:', error);
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
