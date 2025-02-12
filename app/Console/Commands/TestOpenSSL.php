<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestOpenSSL extends Command
{
    protected $signature = 'test:openssl';
    protected $description = 'Teste usando OpenSSL diretamente';

    public function handle()
    {
        $this->info('Testando conexão com OpenSSL...');

        $url = 'https://cdpj.partners.bancointer.com.br/oauth/v2/token';
        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');

        $clientId = config('services.banco_inter.client_id');
        $clientSecret = config('services.banco_inter.client_secret');

        $postData = http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => 'boleto-cobranca.write boleto-cobranca.read'
        ]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        // Configurações SSL
        curl_setopt($ch, CURLOPT_SSLCERT, $certPath);
        curl_setopt($ch, CURLOPT_SSLKEY, $keyPath);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Adicionar headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $this->info('Enviando requisição...');
        
        $response = curl_exec($ch);
        
        if ($response === false) {
            $this->error('Erro cURL:');
            $this->error(curl_error($ch));
            $this->error('Código do erro: ' . curl_errno($ch));
        } else {
            $this->info('Resposta do servidor:');
            $this->line($response);
        }

        curl_close($ch);
    }
}
