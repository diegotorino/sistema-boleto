<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Exception;
use Illuminate\Support\Facades\Log;

class TestPKCS12 extends Command
{
    protected $signature = 'test:pkcs12';
    protected $description = 'Teste usando certificado em formato PKCS12';

    public function handle()
    {
        $this->info('Testando com certificado em formato PKCS12...');

        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');
        $p12Path = storage_path('app/private/certificate.p12');

        // Ler o certificado e a chave
        $cert = file_get_contents($certPath);
        $key = file_get_contents($keyPath);

        // Carregar o certificado e a chave
        $certResource = openssl_x509_read($cert);
        $keyResource = openssl_pkey_get_private($key);

        if (!$certResource || !$keyResource) {
            $this->error('Erro ao carregar certificado ou chave');
            return;
        }

        // Converter para PKCS12
        $p12 = '';
        if (!openssl_pkcs12_export($certResource, $p12, $keyResource, 'senha123')) {
            $this->error('Erro ao converter para PKCS12');
            while ($msg = openssl_error_string()) {
                $this->error($msg);
            }
            return;
        }

        // Salvar o arquivo PKCS12
        file_put_contents($p12Path, $p12);

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
                'cert' => [
                    'file' => $p12Path,
                    'password' => 'senha123'
                ],
                'handler' => $stack,
                'debug' => true,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSLCERTTYPE => 'P12',
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

        // Limpar recursos
        openssl_x509_free($certResource);
        openssl_pkey_free($keyResource);
        @unlink($p12Path);
    }
}
