<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Exception;
use Illuminate\Support\Facades\Log;

class TestBase64 extends Command
{
    protected $signature = 'test:base64';
    protected $description = 'Teste usando certificado em base64';

    public function handle()
    {
        $this->info('Testando com certificado em base64...');

        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');
        $base64Path = storage_path('app/private/cert_base64.txt');

        // Ler o certificado e a chave
        $cert = file_get_contents($certPath);
        $key = file_get_contents($keyPath);

        // Converter para base64
        $certBase64 = base64_encode($cert);
        $keyBase64 = base64_encode($key);

        // Salvar em um arquivo
        file_put_contents($base64Path, "-----BEGIN CERTIFICATE-----\n");
        file_put_contents($base64Path, chunk_split($certBase64, 64), FILE_APPEND);
        file_put_contents($base64Path, "-----END CERTIFICATE-----\n", FILE_APPEND);
        file_put_contents($base64Path, "-----BEGIN PRIVATE KEY-----\n", FILE_APPEND);
        file_put_contents($base64Path, chunk_split($keyBase64, 64), FILE_APPEND);
        file_put_contents($base64Path, "-----END PRIVATE KEY-----\n", FILE_APPEND);

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
                'verify' => false,
                'cert' => $base64Path,
                'handler' => $stack,
                'debug' => true,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => false,
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
