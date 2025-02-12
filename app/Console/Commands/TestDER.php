<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Exception;
use Illuminate\Support\Facades\Log;

class TestDER extends Command
{
    protected $signature = 'test:der';
    protected $description = 'Teste usando certificado em formato DER';

    public function handle()
    {
        $this->info('Testando com certificado em formato DER...');

        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');

        // Ler o certificado e a chave
        $cert = file_get_contents($certPath);
        $key = file_get_contents($keyPath);

        // Converter certificado para DER
        $certResource = openssl_x509_read($cert);
        if (!$certResource) {
            $this->error('Erro ao ler o certificado');
            return;
        }

        // Converter chave para DER
        $keyResource = openssl_pkey_get_private($key);
        if (!$keyResource) {
            $this->error('Erro ao ler a chave privada');
            return;
        }

        // Exportar certificado em formato DER
        $derCertPath = storage_path('app/private/cert.der');
        $derKeyPath = storage_path('app/private/key.der');

        // Exportar certificado
        openssl_x509_export($certResource, $pemCert);
        $derCert = openssl_x509_read($pemCert);
        if (!$derCert) {
            $this->error('Erro ao converter certificado para DER');
            return;
        }

        // Exportar chave
        openssl_pkey_export($keyResource, $pemKey);
        $derKey = openssl_pkey_get_private($pemKey);
        if (!$derKey) {
            $this->error('Erro ao converter chave para DER');
            return;
        }

        // Salvar em arquivos
        file_put_contents($derCertPath, $derCert);
        file_put_contents($derKeyPath, $derKey);

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
                'cert' => [$derCertPath, $derKeyPath],
                'handler' => $stack,
                'debug' => true,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSLCERTTYPE => 'DER',
                    CURLOPT_SSLKEYTYPE => 'DER',
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
        @unlink($derCertPath);
        @unlink($derKeyPath);
    }
}
