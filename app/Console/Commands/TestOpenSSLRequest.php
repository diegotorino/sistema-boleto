<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestOpenSSLRequest extends Command
{
    protected $signature = 'test:openssl-request';
    protected $description = 'Teste usando OpenSSL diretamente';

    public function handle()
    {
        $this->info('Testando com OpenSSL diretamente...');

        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');
        $caPath = storage_path('app/private/inter_ca.crt');

        $url = 'https://cdpj.partners.bancointer.com.br/oauth/v2/token';
        $clientId = config('services.banco_inter.client_id');
        $clientSecret = config('services.banco_inter.client_secret');

        $postData = http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => 'boleto-cobranca.write boleto-cobranca.read'
        ]);

        // Criar contexto SSL
        $context = stream_context_create([
            'ssl' => [
                'local_cert' => $certPath,
                'local_pk' => $keyPath,
                'cafile' => $caPath,
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Content-Length: ' . strlen($postData)
                ],
                'content' => $postData
            ]
        ]);

        $this->info('Enviando requisição...');

        try {
            $response = file_get_contents($url, false, $context);
            if ($response === false) {
                $this->error('Erro ao fazer a requisição');
                $this->error(error_get_last()['message']);
            } else {
                $this->info('Resposta do servidor:');
                $this->line($response);
            }
        } catch (\Exception $e) {
            $this->error('Erro:');
            $this->error($e->getMessage());
        }
    }
}
