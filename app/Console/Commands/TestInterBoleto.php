<?php

namespace App\Console\Commands;

use App\Services\InterService;
use Illuminate\Console\Command;
use Exception;

class TestInterBoleto extends Command
{
    protected $signature = 'inter:test-boleto';
    protected $description = 'Testa a integração com o Banco Inter';

    private $interService;

    public function __construct(InterService $interService)
    {
        parent::__construct();
        $this->interService = $interService;
    }

    public function handle()
    {
        $this->info('Iniciando teste de integração com o Banco Inter...');

        try {
            $this->info('Criando boleto...');
            
            $boleto = $this->interService->createBoleto([
                'seuNumero' => '123456',
                'valorNominal' => 100.00,
                'dataVencimento' => '2025-03-01',
                'numDiasAgenda' => 60,
                'pagador' => [
                    'cpfCnpj' => '12345678909',
                    'tipoPessoa' => 'FISICA',
                    'nome' => 'Nome do Pagador',
                    'endereco' => 'Rua Teste, 123',
                    'cidade' => 'São Paulo',
                    'uf' => 'SP',
                    'cep' => '01234567'
                ]
            ]);

            $this->info('Boleto criado com sucesso!');
            $this->info('Nosso número: ' . $boleto['nossoNumero']);

        } catch (Exception $e) {
            $this->error('Erro ao testar integração: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
