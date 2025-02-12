<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestSystem extends Command
{
    protected $signature = 'test:system';
    protected $description = 'Teste usando certificado do sistema';

    public function handle()
    {
        $this->info('Testando com certificado do sistema...');

        $url = 'https://cdpj.partners.bancointer.com.br/oauth/v2/token';
        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');

        // Verificar se o certificado e a chave existem
        if (!file_exists($certPath)) {
            $this->error('Certificado não encontrado: ' . $certPath);
            return;
        }
        if (!file_exists($keyPath)) {
            $this->error('Chave não encontrada: ' . $keyPath);
            return;
        }

        // Exibir informações do certificado
        $cert = openssl_x509_read(file_get_contents($certPath));
        if ($cert) {
            $certInfo = openssl_x509_parse($cert);
            $this->info('Informações do certificado:');
            $this->line('Subject: ' . $certInfo['subject']['CN']);
            $this->line('Issuer: ' . $certInfo['issuer']['CN']);
            $this->line('Valid From: ' . date('Y-m-d H:i:s', $certInfo['validFrom_time_t']));
            $this->line('Valid To: ' . date('Y-m-d H:i:s', $certInfo['validTo_time_t']));
        } else {
            $this->error('Erro ao ler o certificado');
            while ($msg = openssl_error_string()) {
                $this->error($msg);
            }
            return;
        }

        $clientId = config('services.banco_inter.client_id');
        $clientSecret = config('services.banco_inter.client_secret');

        $postData = http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => 'boleto-cobranca.write boleto-cobranca.read'
        ]);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
            CURLOPT_VERBOSE => true,
            CURLOPT_STDERR => fopen('php://stdout', 'w'),
            
            // Configurações SSL
            CURLOPT_SSLCERT => $certPath,
            CURLOPT_SSLKEY => $keyPath,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CAINFO => 'C:\Windows\system32\curl-ca-bundle.crt' // Certificado CA do sistema
        ]);

        $this->info('Enviando requisição...');
        
        $response = curl_exec($ch);
        
        if ($response === false) {
            $this->error('Erro cURL:');
            $this->error(curl_error($ch));
            $this->error('Código do erro: ' . curl_errno($ch));

            // Verificar se o erro é relacionado ao certificado
            if (curl_errno($ch) == CURLE_SSL_CACERT) {
                $this->info('Tentando encontrar o certificado CA do sistema...');
                $this->line('OpenSSL Version: ' . OPENSSL_VERSION_TEXT);
                $this->line('SSL Certificate Authorities:');
                $this->line(openssl_get_cert_locations()['default_cert_file'] ?? 'Not found');
            }
        } else {
            $this->info('Resposta do servidor:');
            $this->line($response);
        }

        curl_close($ch);
    }
}
