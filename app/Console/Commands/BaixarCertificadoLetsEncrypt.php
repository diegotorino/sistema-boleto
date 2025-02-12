<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class BaixarCertificadoLetsEncrypt extends Command
{
    protected $signature = 'cert:baixar-lets-encrypt';
    protected $description = 'Baixa o certificado raiz do Let\'s Encrypt';

    public function handle()
    {
        $this->info('Baixando certificado raiz do Let\'s Encrypt...');

        try {
            // URLs dos certificados raiz do Let's Encrypt
            $urls = [
                'https://letsencrypt.org/certs/isrgrootx1.pem',
                'https://letsencrypt.org/certs/lets-encrypt-r3.pem',
                'https://letsencrypt.org/certs/lets-encrypt-e1.pem'
            ];

            $bundle = '';
            $success = false;

            foreach ($urls as $url) {
                $this->info("\nBaixando certificado de: $url");

                try {
                    $response = Http::withoutVerifying()->get($url);
                    
                    if ($response->successful()) {
                        $cert = $response->body();
                        
                        // Verificar se é um certificado válido
                        if ($this->isValidCertificate($cert)) {
                            $bundle .= $cert . "\n";
                            $this->displayCertificateInfo($cert);
                            $success = true;
                        }
                    }
                } catch (\Exception $e) {
                    $this->warn("Erro ao baixar de $url: " . $e->getMessage());
                }
            }

            if (!$success) {
                $this->error('Não foi possível baixar nenhum certificado.');
                return 1;
            }

            // Salvar o bundle
            $bundlePath = storage_path('app/private/lets_encrypt_ca.crt');
            file_put_contents($bundlePath, $bundle);
            
            $this->info("\nBundle de certificados salvo em: $bundlePath");

            // Atualizar o bundle principal
            $interCaPath = storage_path('app/private/inter_ca.crt');
            if (file_exists($interCaPath)) {
                $interCa = file_get_contents($interCaPath);
                $bundle = $interCa . "\n" . $bundle;
                file_put_contents($interCaPath, $bundle);
                $this->info("Certificados Let's Encrypt adicionados ao bundle do Inter");
            }

            // Configurar o PHP para usar o novo certificado
            $this->info("\nConfigurando PHP para usar o novo certificado...");
            
            // Tentar atualizar php.ini
            if (ini_set('curl.cainfo', $interCaPath)) {
                $this->info('curl.cainfo atualizado com sucesso!');
            } else {
                $this->warn('Não foi possível atualizar curl.cainfo');
            }
            
            if (ini_set('openssl.cafile', $interCaPath)) {
                $this->info('openssl.cafile atualizado com sucesso!');
            } else {
                $this->warn('Não foi possível atualizar openssl.cafile');
            }

            // Atualizar o arquivo de configuração do Laravel
            $this->updateLaravelConfig($interCaPath);

            return 0;

        } catch (\Exception $e) {
            $this->error('Erro: ' . $e->getMessage());
            return 1;
        }
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

    private function displayCertificateInfo($cert)
    {
        $certResource = openssl_x509_read($cert);
        $certInfo = openssl_x509_parse($certResource);

        $this->info("\nInformações do certificado:");
        $this->line("Subject: " . ($certInfo['subject']['CN'] ?? 'N/A'));
        $this->line("Issuer: " . ($certInfo['issuer']['CN'] ?? 'N/A'));
        $this->line("Valid From: " . date('Y-m-d H:i:s', $certInfo['validFrom_time_t']));
        $this->line("Valid To: " . date('Y-m-d H:i:s', $certInfo['validTo_time_t']));

        openssl_x509_free($certResource);
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
