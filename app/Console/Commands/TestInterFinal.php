<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Exception;
use Illuminate\Support\Facades\Log;

class TestInterFinal extends Command
{
    protected $signature = 'test:inter-final';
    protected $description = 'Teste final da conexão com o Banco Inter usando CA';

    public function handle()
    {
        $this->info('Testando conexão com o Banco Inter...');

        try {
            // Criar um handler stack para adicionar middleware
            $stack = HandlerStack::create();
            
            // Adicionar middleware de logging
            $stack->push(
                Middleware::log(
                    Log::channel('daily'),
                    new MessageFormatter(
                        "Request: {method} {uri}\nBody: {req_body}\nResponse: {code}\nBody: {res_body}"
                    )
                )
            );

            $client = new Client([
                'verify' => storage_path('app/private/inter_ca.crt'),
                'cert' => [
                    storage_path('app/private/Sandbox_InterAPI_Certificado.crt'),
                    storage_path('app/private/Sandbox_InterAPI_Chave.key')
                ],
                'handler' => $stack,
                'debug' => true,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                ],
            ]);

            $this->info('Tentando obter token...');

            $response = $client->post('https://cdpj.partners.bancointer.com.br/oauth/v2/token', [
                'form_params' => [
                    'client_id' => config('services.banco_inter.client_id'),
                    'client_secret' => config('services.banco_inter.client_secret'),
                    'grant_type' => 'client_credentials',
                    'scope' => 'boleto-cobranca.write boleto-cobranca.read',
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            $this->info('Resposta do servidor:');
            $this->line(json_encode($data, JSON_PRETTY_PRINT));

            // Se chegou até aqui, vamos tentar criar um boleto
            if (!empty($data['access_token'])) {
                $this->info("\nTentando criar um boleto de teste...");

                $response = $client->post('https://cdpj.partners.bancointer.com.br/cobranca/v2/boletos', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $data['access_token'],
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'seuNumero' => 'TEST' . time(),
                        'valorNominal' => 100.00,
                        'dataVencimento' => '2025-03-01',
                        'numDiasAgenda' => 60,
                        'pagador' => [
                            'cpfCnpj' => '12345678909',
                            'tipoPessoa' => 'FISICA',
                            'nome' => 'Nome do Pagador',
                            'endereco' => 'Rua Teste',
                            'numero' => '123',
                            'bairro' => 'Bairro',
                            'cidade' => 'Cidade',
                            'uf' => 'MG',
                            'cep' => '30000000',
                            'email' => 'teste@teste.com',
                            'ddd' => '31',
                            'telefone' => '999999999'
                        ],
                        'mensagem' => [
                            'linha1' => 'Teste de integração',
                            'linha2' => 'Boleto de teste',
                            'linha3' => 'Não pagar',
                            'linha4' => 'Sistema de boletos',
                            'linha5' => date('Y-m-d H:i:s')
                        ]
                    ]
                ]);

                $boletoData = json_decode($response->getBody(), true);
                
                $this->info('Boleto criado com sucesso!');
                $this->line(json_encode($boletoData, JSON_PRETTY_PRINT));
            }

        } catch (Exception $e) {
            $this->error('Erro:');
            $this->error($e->getMessage());
            
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $this->error('Resposta do servidor:');
                $this->error($e->getResponse()->getBody()->getContents());
            }
        }
    }
}
