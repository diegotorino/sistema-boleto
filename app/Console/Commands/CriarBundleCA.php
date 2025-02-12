<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CriarBundleCA extends Command
{
    protected $signature = 'cert:criar-bundle';
    protected $description = 'Cria um bundle de certificados CA usando os certificados do Windows';

    public function handle()
    {
        $this->info('Criando bundle de certificados CA...');

        // Diretório onde o bundle será salvo
        $bundlePath = storage_path('app/private/ca-bundle.crt');
        $bundle = '';

        // Diretórios de certificados do Windows
        $windowsCertDirs = [
            'C:\\Windows\\System32\\certSrv\\CertEnroll',
            'C:\\Windows\\System32\\spool\\drivers\\color',
            'C:\\Windows\\System32\\spool\\PRINTERS',
            'C:\\Windows\\System32\\catroot',
            'C:\\Windows\\System32\\catroot2',
            'C:\\Windows\\System32\\GroupPolicy\\Machine\\Scripts',
            'C:\\Windows\\System32\\Microsoft\\Protect',
            'C:\\Windows\\System32\\Spool\\Prtprocs\\x64',
            'C:\\Windows\\System32\\sru'
        ];

        foreach ($windowsCertDirs as $dir) {
            if (file_exists($dir)) {
                $this->info("Procurando certificados em: $dir");
                $files = glob($dir . '\\*.{crt,cer,pem}', GLOB_BRACE);
                foreach ($files as $file) {
                    $this->line("  Processando: " . basename($file));
                    $cert = file_get_contents($file);
                    if ($this->isValidCertificate($cert)) {
                        $bundle .= $cert . "\n";
                    }
                }
            }
        }

        // Adicionar certificado do próprio Inter
        $interCert = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        if (file_exists($interCert)) {
            $this->info('Adicionando certificado do Inter ao bundle...');
            $cert = file_get_contents($interCert);
            if ($this->isValidCertificate($cert)) {
                $bundle .= $cert . "\n";
            }
        }

        // Adicionar certificado CA do servidor
        $serverCert = storage_path('app/private/inter_ca.crt');
        if (file_exists($serverCert)) {
            $this->info('Adicionando certificado CA do servidor ao bundle...');
            $cert = file_get_contents($serverCert);
            if ($this->isValidCertificate($cert)) {
                $bundle .= $cert . "\n";
            }
        }

        // Se não encontrou nenhum certificado, tentar usar o certificado do cURL
        if (empty($bundle)) {
            $curlCaInfo = ini_get('curl.cainfo');
            if ($curlCaInfo && file_exists($curlCaInfo)) {
                $this->info('Usando certificado CA do cURL: ' . $curlCaInfo);
                $bundle = file_get_contents($curlCaInfo);
            }
        }

        // Se ainda não encontrou nenhum certificado, usar o certificado do próprio Inter
        if (empty($bundle)) {
            $this->warn('Nenhum certificado encontrado. Usando apenas o certificado do Inter.');
            if (file_exists($interCert)) {
                $bundle = file_get_contents($interCert);
            }
        }

        // Salvar o bundle
        file_put_contents($bundlePath, $bundle);
        $this->info("Bundle de certificados salvo em: $bundlePath");

        // Atualizar o php.ini
        $this->info('Atualizando php.ini...');
        if (ini_set('curl.cainfo', $bundlePath)) {
            $this->info('curl.cainfo atualizado com sucesso!');
        } else {
            $this->warn('Não foi possível atualizar curl.cainfo');
        }
        if (ini_set('openssl.cafile', $bundlePath)) {
            $this->info('openssl.cafile atualizado com sucesso!');
        } else {
            $this->warn('Não foi possível atualizar openssl.cafile');
        }

        // Atualizar o arquivo de configuração do Laravel
        $this->updateLaravelConfig($bundlePath);

        return 0;
    }

    private function isValidCertificate($cert)
    {
        $certResource = @openssl_x509_read($cert);
        if (!$certResource) {
            return false;
        }

        $certInfo = openssl_x509_parse($certResource);
        openssl_x509_free($certResource);

        return !empty($certInfo);
    }

    private function updateLaravelConfig($bundlePath)
    {
        $configPath = config_path('services.php');
        
        if (!file_exists($configPath)) {
            $this->warn('Arquivo de configuração services.php não encontrado.');
            return;
        }

        $config = file_get_contents($configPath);
        $config = preg_replace(
            "/'ca_bundle' => '.*?'/",
            "'ca_bundle' => '" . str_replace('\\', '\\\\', $bundlePath) . "'",
            $config
        );

        file_put_contents($configPath, $config);
        $this->info('Arquivo de configuração services.php atualizado com sucesso!');
    }
}
