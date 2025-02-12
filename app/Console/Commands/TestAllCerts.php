<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Exception;
use Illuminate\Support\Facades\Log;

class TestAllCerts extends Command
{
    protected $signature = 'test:all-certs';
    protected $description = 'Testa todas as combinações de certificados';

    private function testConfig($config)
    {
        $this->info("\nTestando configuração:");
        $this->line(json_encode($config, JSON_PRETTY_PRINT));

        try {
            $stack = HandlerStack::create();
            $stack->push(
                Middleware::log(
                    Log::channel('daily'),
                    new MessageFormatter(
                        "Request: {method} {uri}\nBody: {req_body}\nResponse: {code}\nBody: {res_body}"
                    )
                )
            );

            $client = new Client(array_merge([
                'handler' => $stack,
                'debug' => true,
            ], $config));

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
            $this->info('Sucesso!');
            $this->line(json_encode($data, JSON_PRETTY_PRINT));
            return true;
        } catch (Exception $e) {
            $this->error('Erro:');
            $this->error($e->getMessage());
            
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $this->error('Resposta do servidor:');
                $this->error($e->getResponse()->getBody()->getContents());
            }
            return false;
        }
    }

    public function handle()
    {
        $this->info('Testando todas as combinações de certificados...');

        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');
        $caPath = storage_path('app/private/inter_ca.crt');
        $pemPath = storage_path('app/private/Sandbox_InterAPI_New.pem');

        $configs = [
            // Teste 1: Certificado e chave separados com CA
            [
                'verify' => $caPath,
                'cert' => [$certPath, $keyPath],
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                ],
            ],
            
            // Teste 2: Certificado PEM com CA
            [
                'verify' => $caPath,
                'cert' => $pemPath,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                ],
            ],
            
            // Teste 3: Certificado e chave separados sem verificação
            [
                'verify' => false,
                'cert' => [$certPath, $keyPath],
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => false,
                ],
            ],
            
            // Teste 4: Certificado PEM sem verificação
            [
                'verify' => false,
                'cert' => $pemPath,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => false,
                ],
            ],
            
            // Teste 5: Apenas certificado e chave
            [
                'cert' => [$certPath, $keyPath],
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                ],
            ],
            
            // Teste 6: Apenas certificado PEM
            [
                'cert' => $pemPath,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                ],
            ],
            
            // Teste 7: Certificado e chave com CA do sistema
            [
                'verify' => true,
                'cert' => [$certPath, $keyPath],
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                ],
            ],
            
            // Teste 8: Certificado PEM com CA do sistema
            [
                'verify' => true,
                'cert' => $pemPath,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                ],
            ],
        ];

        foreach ($configs as $index => $config) {
            $this->info("\n=== Teste " . ($index + 1) . " ===");
            if ($this->testConfig($config)) {
                $this->info('Configuração ' . ($index + 1) . ' funcionou!');
                break;
            }
        }
    }
}
