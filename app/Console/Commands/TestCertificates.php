<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCertificates extends Command
{
    protected $signature = 'test:certificates';
    protected $description = 'Testa os certificados do Banco Inter';

    public function handle()
    {
        $certPath = config('services.banco_inter.certificate_path');
        $keyPath = config('services.banco_inter.certificate_key');

        $this->info('Testando certificados...');
        $this->info('Certificado: ' . $certPath);
        $this->info('Chave: ' . $keyPath);

        // Verificar se os arquivos existem
        if (!file_exists($certPath)) {
            $this->error('Arquivo do certificado não encontrado!');
            return;
        }
        if (!file_exists($keyPath)) {
            $this->error('Arquivo da chave não encontrado!');
            return;
        }

        // Tentar ler os certificados
        $cert = openssl_x509_read(file_get_contents($certPath));
        if ($cert === false) {
            $this->error('Erro ao ler o certificado:');
            while ($msg = openssl_error_string()) {
                $this->error($msg);
            }
        } else {
            $this->info('Certificado lido com sucesso!');
            $certInfo = openssl_x509_parse($cert);
            $this->info('Informações do certificado:');
            $this->line('Subject: ' . $certInfo['subject']['CN']);
            $this->line('Issuer: ' . $certInfo['issuer']['CN']);
            $this->line('Valid From: ' . date('Y-m-d H:i:s', $certInfo['validFrom_time_t']));
            $this->line('Valid To: ' . date('Y-m-d H:i:s', $certInfo['validTo_time_t']));
        }

        // Tentar ler a chave privada
        $key = openssl_pkey_get_private(file_get_contents($keyPath));
        if ($key === false) {
            $this->error('Erro ao ler a chave privada:');
            while ($msg = openssl_error_string()) {
                $this->error($msg);
            }
        } else {
            $this->info('Chave privada lida com sucesso!');
            $keyDetails = openssl_pkey_get_details($key);
            $this->info('Informações da chave:');
            $this->line('Bits: ' . $keyDetails['bits']);
            $this->line('Type: ' . $keyDetails['type']);
        }
    }
}
