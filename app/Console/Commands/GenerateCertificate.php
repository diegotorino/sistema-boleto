<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateCertificate extends Command
{
    protected $signature = 'cert:generate';
    protected $description = 'Gera um novo certificado PEM usando OpenSSL';

    public function handle()
    {
        $this->info('Gerando novo certificado...');

        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');
        $pemPath = storage_path('app/private/Sandbox_InterAPI_New.pem');

        // Ler o certificado e a chave
        $cert = file_get_contents($certPath);
        $key = file_get_contents($keyPath);

        // Carregar o certificado
        $certResource = openssl_x509_read($cert);
        if (!$certResource) {
            $this->error('Erro ao ler o certificado:');
            while ($msg = openssl_error_string()) {
                $this->error($msg);
            }
            return;
        }

        // Carregar a chave privada
        $keyResource = openssl_pkey_get_private($key);
        if (!$keyResource) {
            $this->error('Erro ao ler a chave privada:');
            while ($msg = openssl_error_string()) {
                $this->error($msg);
            }
            return;
        }

        // Verificar se a chave corresponde ao certificado
        if (!openssl_x509_check_private_key($certResource, $keyResource)) {
            $this->error('A chave privada não corresponde ao certificado!');
            return;
        }

        $this->info('Certificado e chave validados com sucesso!');

        // Exportar o certificado e a chave em formato PEM
        openssl_x509_export($certResource, $pemCert);
        openssl_pkey_export($keyResource, $pemKey);

        // Combinar certificado e chave
        $pem = $pemCert . $pemKey;

        // Salvar o arquivo PEM
        file_put_contents($pemPath, $pem);

        $this->info('Novo certificado gerado com sucesso!');
        $this->info('Arquivo gerado: ' . $pemPath);

        // Exibir informações do certificado
        $certInfo = openssl_x509_parse($certResource);
        $this->info('Informações do certificado:');
        $this->line('Subject: ' . $certInfo['subject']['CN']);
        $this->line('Issuer: ' . $certInfo['issuer']['CN']);
        $this->line('Valid From: ' . date('Y-m-d H:i:s', $certInfo['validFrom_time_t']));
        $this->line('Valid To: ' . date('Y-m-d H:i:s', $certInfo['validTo_time_t']));

        // Limpar recursos
        openssl_x509_free($certResource);
        openssl_pkey_free($keyResource);
    }
}
