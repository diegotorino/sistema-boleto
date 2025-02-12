<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Exception;
use Illuminate\Support\Facades\Log;

class TestString extends Command
{
    protected $signature = 'test:string';
    protected $description = 'Teste usando certificado como string';

    public function handle()
    {
        $this->info('Testando com certificado como string...');

        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');

        // Ler o certificado e a chave
        $cert = file_get_contents($certPath);
        $key = file_get_contents($keyPath);

        // Criar arquivo temporário para o certificado
        $tempCertPath = storage_path('app/private/temp_cert.pem');
        $tempKeyPath = storage_path('app/private/temp_key.pem');

        // Garantir que o certificado e a chave estão no formato correto
        if (!str_starts_with(trim($cert), '-----BEGIN CERTIFICATE-----')) {
            $cert = "-----BEGIN CERTIFICATE-----\n" . trim($cert) . "\n-----END CERTIFICATE-----\n";
        }
        if (!str_starts_with(trim($key), '-----BEGIN PRIVATE KEY-----')) {
            $key = "-----BEGIN PRIVATE KEY-----\n" . trim($key) . "\n-----END PRIVATE KEY-----\n";
        }

        // Salvar em arquivos temporários
        file_put_contents($tempCertPath, $cert);
        file_put_contents($tempKeyPath, $key);

        // Verificar se os arquivos foram criados corretamente
        $this->info('Conteúdo do certificado:');
        $this->line(file_get_contents($tempCertPath));
        $this->info('Conteúdo da chave:');
        $this->line(file_get_contents($tempKeyPath));

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
                'cert' => [$tempCertPath, $tempKeyPath],
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

        // Limpar arquivos temporários
        @unlink($tempCertPath);
        @unlink($tempKeyPath);
    }
}
