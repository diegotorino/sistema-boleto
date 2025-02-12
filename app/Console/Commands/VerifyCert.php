<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VerifyCert extends Command
{
    protected $signature = 'cert:verify';
    protected $description = 'Verifica o certificado e a chave';

    public function handle()
    {
        $this->info('Verificando certificado e chave...');

        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');

        // Verificar se os arquivos existem
        if (!file_exists($certPath)) {
            $this->error('Certificado não encontrado: ' . $certPath);
            return;
        }
        if (!file_exists($keyPath)) {
            $this->error('Chave não encontrada: ' . $keyPath);
            return;
        }

        // Ler o certificado e a chave
        $cert = file_get_contents($certPath);
        $key = file_get_contents($keyPath);

        // Verificar o certificado
        $certResource = openssl_x509_read($cert);
        if (!$certResource) {
            $this->error('Erro ao ler o certificado:');
            while ($msg = openssl_error_string()) {
                $this->error($msg);
            }
            return;
        }

        // Verificar a chave
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

        // Exibir informações do certificado
        $certInfo = openssl_x509_parse($certResource);
        $this->info('Informações do certificado:');
        $this->line('Subject: ' . $certInfo['subject']['CN']);
        $this->line('Issuer: ' . $certInfo['issuer']['CN']);
        $this->line('Valid From: ' . date('Y-m-d H:i:s', $certInfo['validFrom_time_t']));
        $this->line('Valid To: ' . date('Y-m-d H:i:s', $certInfo['validTo_time_t']));

        // Exibir informações da chave
        $keyDetails = openssl_pkey_get_details($keyResource);
        $this->info('Informações da chave:');
        $this->line('Tipo: ' . $this->getKeyType($keyDetails['type']));
        $this->line('Bits: ' . $keyDetails['bits']);

        // Verificar o formato do certificado
        $this->info('Formato do certificado:');
        $this->line(trim($cert));

        // Verificar o formato da chave
        $this->info('Formato da chave:');
        $this->line(trim($key));

        // Limpar recursos
        openssl_x509_free($certResource);
        openssl_pkey_free($keyResource);
    }

    private function getKeyType($type)
    {
        switch ($type) {
            case OPENSSL_KEYTYPE_RSA:
                return 'RSA';
            case OPENSSL_KEYTYPE_DSA:
                return 'DSA';
            case OPENSSL_KEYTYPE_DH:
                return 'DH';
            case OPENSSL_KEYTYPE_EC:
                return 'EC';
            default:
                return 'Unknown';
        }
    }
}
