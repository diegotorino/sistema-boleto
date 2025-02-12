<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCurl extends Command
{
    protected $signature = 'test:curl';
    protected $description = 'Testa a conexão com o Banco Inter usando cURL diretamente';

    public function handle()
    {
        $this->info('Testando conexão com o Banco Inter usando cURL...');

        $url = 'https://cdpj.partners.bancointer.com.br/oauth/v2/token';
        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');
        $caPath = storage_path('app/private/Sandbox_InterAPI_CA.crt');

        $this->info('Certificado: ' . $certPath);
        $this->info('Chave: ' . $keyPath);
        $this->info('CA: ' . $caPath);

        $ch = curl_init();

        $postData = http_build_query([
            'client_id' => config('services.banco_inter.client_id'),
            'client_secret' => config('services.banco_inter.client_secret'),
            'grant_type' => 'client_credentials',
            'scope' => 'boleto-cobranca.write boleto-cobranca.read',
        ]);

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSLCERT => $certPath,
            CURLOPT_SSLKEY => $keyPath,
            CURLOPT_CAINFO => $caPath,
            CURLOPT_VERBOSE => true,
        ]);

        $this->info('Enviando requisição...');
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);

        if ($error) {
            $this->error('Erro cURL:');
            $this->error($error);
        } else {
            $this->info('Resposta:');
            $this->line($response);
        }

        $this->info('Informações da requisição:');
        $this->line(json_encode($info, JSON_PRETTY_PRINT));

        curl_close($ch);
    }
}
