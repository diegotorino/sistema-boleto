<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestarSSL extends Command
{
    protected $signature = 'test:ssl';
    protected $description = 'Testa a conexão SSL com o servidor do Banco Inter';

    public function handle()
    {
        $this->info('Testando conexão SSL com o servidor do Banco Inter...');

        $host = 'cdpj.partners.bancointer.com.br';
        $port = 443;

        // Testar conexão básica
        $this->info("\nTestando conexão básica...");
        $fp = fsockopen($host, $port, $errno, $errstr, 30);
        if (!$fp) {
            $this->error("Erro na conexão básica: $errstr ($errno)");
            return 1;
        }
        $this->info("Conexão básica OK!");
        fclose($fp);

        // Testar conexão SSL
        $this->info("\nTestando conexão SSL...");
        $fp = fsockopen("ssl://$host", $port, $errno, $errstr, 30);
        if (!$fp) {
            $this->error("Erro na conexão SSL: $errstr ($errno)");
            return 1;
        }
        $this->info("Conexão SSL OK!");
        fclose($fp);

        // Testar certificados
        $this->info("\nTestando certificados...");
        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');

        // Verificar se os certificados existem
        if (!file_exists($certPath)) {
            $this->error("Certificado não encontrado em: $certPath");
            return 1;
        }
        if (!file_exists($keyPath)) {
            $this->error("Chave privada não encontrada em: $keyPath");
            return 1;
        }

        // Testar leitura dos certificados
        $cert = openssl_x509_read(file_get_contents($certPath));
        if (!$cert) {
            $this->error("Erro ao ler o certificado:");
            while ($msg = openssl_error_string()) {
                $this->error($msg);
            }
            return 1;
        }
        $this->info("Certificado lido com sucesso!");

        $key = openssl_pkey_get_private(file_get_contents($keyPath));
        if (!$key) {
            $this->error("Erro ao ler a chave privada:");
            while ($msg = openssl_error_string()) {
                $this->error($msg);
            }
            return 1;
        }
        $this->info("Chave privada lida com sucesso!");

        // Testar se a chave corresponde ao certificado
        if (!openssl_x509_check_private_key($cert, $key)) {
            $this->error("A chave privada não corresponde ao certificado!");
            return 1;
        }
        $this->info("Chave privada corresponde ao certificado!");

        // Testar conexão com cURL
        $this->info("\nTestando conexão com cURL...");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://$host");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLCERT, $certPath);
        curl_setopt($ch, CURLOPT_SSLKEY, $keyPath);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        // Criar um stream temporário para capturar o output verbose
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);

        // Ler o output verbose
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        fclose($verbose);

        if ($error) {
            $this->error("Erro cURL: $error");
            $this->line("\nInformações detalhadas:");
            $this->line($verboseLog);
            curl_close($ch);
            return 1;
        }

        $this->info("Conexão cURL OK!");
        $this->line("\nInformações da conexão:");
        $this->line("HTTP Code: " . $info['http_code']);
        $this->line("SSL Verify Result: " . $info['ssl_verify_result']);
        $this->line("Certinfo: " . json_encode($info['certinfo'] ?? []));
        $this->line("\nLog detalhado:");
        $this->line($verboseLog);

        curl_close($ch);

        // Verificar versão do OpenSSL
        $this->info("\nInformações do OpenSSL:");
        $this->line("Versão: " . OPENSSL_VERSION_TEXT);
        $this->line("Versões suportadas: " . implode(', ', openssl_get_cipher_methods()));

        return 0;
    }
}
