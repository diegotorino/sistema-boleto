<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InterBoletoService;
use App\Services\InterTokenService;
use Illuminate\Support\Facades\Log;

class TestInterBoleto extends Command
{
    protected $signature = 'inter:test-boleto';
    protected $description = 'Testa a integração com o Banco Inter para geração de boletos';

    protected $boletoService;

    public function __construct(InterBoletoService $boletoService)
    {
        parent::__construct();
        $this->boletoService = $boletoService;
    }

    protected function displayError($result)
    {
        $this->error($result['message']);

        if (isset($result['status'])) {
            $this->line('Status: ' . $result['status']);
        }

        if (isset($result['errors']) && is_array($result['errors'])) {
            $this->line('Detalhes do erro:');
            foreach ($result['errors'] as $key => $value) {
                if (is_array($value)) {
                    $this->line("- {$key}: " . json_encode($value, JSON_PRETTY_PRINT));
                } else {
                    $this->line("- {$key}: {$value}");
                }
            }
        }
    }

    public function handle()
    {
        $this->info('Iniciando teste de integração com Banco Inter...');

        try {
            $data = [
                'seuNumero' => 'TEST' . time(),
                'valorNominal' => '150.00',
                'dataVencimento' => '2025-02-20',
                'numDiasAgenda' => '60',
                'pagador' => [
                    'nome' => 'Diego Torino',
                    'tipoPessoa' => 'FISICA',
                    'cpfCnpj' => '45762299821',
                    'email' => 'diegodelima319@gmail.com',
                    'endereco' => [
                        'cep' => '04657000',
                        'logradouro' => 'Av Yervant kissajikian',
                        'numero' => '299',
                        'complemento' => 'bloco c apto 56',
                        'bairro' => 'Vila Constança',
                        'cidade' => 'São Paulo',
                        'uf' => 'SP'
                    ]
                ]
            ];

            $this->info('Criando boleto...');
            $result = $this->boletoService->createBoleto($data);

            if (!$result['success']) {
                $this->displayError($result);
                return 1;
            }

            $this->info('Boleto criado com sucesso!');
            $this->info('Detalhes do boleto:');
            $this->table(['Campo', 'Valor'], collect($result['data'])->map(function ($value, $key) {
                return [$key, is_array($value) ? json_encode($value) : $value];
            })->toArray());

            $codigoSolicitacao = $result['data']['codigoSolicitacao'];
            $this->info('Obtendo PDF do boleto...');
            
            $pdfResult = $this->boletoService->getBoletoDetails($codigoSolicitacao);

            if (!$pdfResult['success']) {
                $this->displayError($pdfResult);
                return 1;
            }

            $this->info('PDF gerado com sucesso em: ' . $pdfResult['data']['pdf_path']);

            return 0;

        } catch (\Exception $e) {
            $this->error('Erro durante o teste: ' . $e->getMessage());
            Log::error('Erro durante o teste de boleto:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
