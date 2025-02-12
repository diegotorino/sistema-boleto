<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCurlCombined extends Command
{
    protected $signature = 'test:curl-combined';
    protected $description = 'Testa a conexão com o Banco Inter usando cURL com certificado combinado';

    public function handle()
    {
        $this->info('Testando conexão com o Banco Inter usando cURL...');

        $url = 'https://cdpj.partners.bancointer.com.br/oauth/v2/token';
        $combinedPath = storage_path('app/private/Sandbox_InterAPI_Combined.pem');

        $this->info('Certificado combinado: ' . $combinedPath);

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
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLCERT => $combinedPath,
            CURLOPT_VERBOSE => true,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
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
