<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PrepararCertificados extends Command
{
    protected $signature = 'cert:preparar';
    protected $description = 'Prepara os certificados do Banco Inter';

    public function handle()
    {
        $this->info('Preparando certificados do Banco Inter...');

        // Diretório onde os certificados serão armazenados
        $certDir = storage_path('app/private');
        if (!file_exists($certDir)) {
            mkdir($certDir, 0777, true);
        }

        // Verificar certificados existentes
        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');
        $caPath = storage_path('app/private/inter_ca.crt');

        if (!file_exists($certPath) || !file_exists($keyPath)) {
            $this->error('Certificado ou chave privada não encontrados!');
            $this->info('Por favor, coloque os arquivos:');
            $this->line("- Sandbox_InterAPI_Certificado.crt em: {$certPath}");
            $this->line("- Sandbox_InterAPI_Chave.key em: {$keyPath}");
            return 1;
        }

        // Verificar se o certificado e a chave são válidos
        $cert = file_get_contents($certPath);
        $key = file_get_contents($keyPath);

        $certResource = openssl_x509_read($cert);
        if (!$certResource) {
            $this->error('Erro ao ler o certificado:');
            while ($msg = openssl_error_string()) {
                $this->error($msg);
            }
            return 1;
        }

        $keyResource = openssl_pkey_get_private($key);
        if (!$keyResource) {
            $this->error('Erro ao ler a chave privada:');
            while ($msg = openssl_error_string()) {
                $this->error($msg);
            }
            return 1;
        }

        // Verificar se a chave corresponde ao certificado
        if (!openssl_x509_check_private_key($certResource, $keyResource)) {
            $this->error('A chave privada não corresponde ao certificado!');
            return 1;
        }

        $this->info('Certificado e chave validados com sucesso!');

        // Tentar usar o certificado CA do sistema
        $systemCaPath = 'C:\\Windows\\System32\\curl-ca-bundle.crt';
        if (file_exists($systemCaPath)) {
            $this->info('Usando certificado CA do sistema...');
            copy($systemCaPath, $caPath);
        } else {
            // Se não encontrar, criar um bundle com os certificados do sistema
            $this->info('Criando bundle de certificados CA...');
            $bundle = '';
            
            // Diretório de certificados do Windows
            $windowsCertDir = 'C:\\Windows\\System32\\certSrv\\CertEnroll';
            if (file_exists($windowsCertDir)) {
                $files = glob($windowsCertDir . '\\*.crt');
                foreach ($files as $file) {
                    $bundle .= file_get_contents($file) . "\n";
                }
            }
            
            // Se não encontrou nenhum certificado, usar o certificado do próprio Inter
            if (empty($bundle)) {
                $bundle = $cert;
            }
            
            file_put_contents($caPath, $bundle);
        }

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

        // Limpar recursos
        openssl_x509_free($certResource);
        openssl_pkey_free($keyResource);

        $this->info('Certificados preparados com sucesso!');
        return 0;
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
