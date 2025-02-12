<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BaixarCertificadoInterV3 extends Command
{
    protected $signature = 'cert:baixar-inter-v3';
    protected $description = 'Baixa o certificado do Banco Inter (V3)';

    public function handle()
    {
        $this->info('Baixando certificado do Banco Inter (V3)...');

        try {
            $host = 'cdpj.partners.bancointer.com.br';
            $port = 443;

            // Criar contexto SSL sem verificação
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'capture_peer_cert_chain' => true,
                    'capture_peer_cert' => true,
                    'disable_compression' => true,
                    'ciphers' => 'HIGH:!SSLv2:!SSLv3:!TLSv1:!TLSv1.1',
                    'security_level' => 1
                ]
            ]);

            $this->info("Conectando a {$host}:{$port}...");

            $socket = @stream_socket_client(
                "ssl://{$host}:{$port}",
                $errno,
                $errstr,
                30,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (!$socket) {
                throw new \Exception("Erro ao conectar: {$errstr} ({$errno})");
            }

            $this->info('Conexão estabelecida com sucesso!');

            $params = stream_context_get_params($socket);
            $success = false;

            if (isset($params['options']['ssl']['peer_certificate_chain'])) {
                $certChain = $params['options']['ssl']['peer_certificate_chain'];
                $bundle = '';
                
                foreach ($certChain as $index => $cert) {
                    $certData = '';
                    openssl_x509_export($cert, $certData);
                    
                    // Salvar cada certificado individualmente
                    $certPath = storage_path("app/private/inter_cert_{$index}.crt");
                    file_put_contents($certPath, $certData);
                    
                    // Adicionar ao bundle
                    $bundle .= $certData . "\n";
                    
                    // Exibir informações do certificado
                    $this->displayCertificateInfo($cert);
                    
                    $success = true;
                }

                if ($success) {
                    // Salvar o bundle
                    $bundlePath = storage_path('app/private/inter_ca.crt');
                    file_put_contents($bundlePath, $bundle);
                    
                    $this->info("\nBundle de certificados salvo em: {$bundlePath}");

                    // Tentar atualizar php.ini
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

                    // Adicionar os certificados do Let's Encrypt
                    $this->info("\nAdicionando certificados Let's Encrypt...");
                    $letsEncryptUrls = [
                        'https://letsencrypt.org/certs/isrgrootx1.pem',
                        'https://letsencrypt.org/certs/lets-encrypt-r3.pem',
                        'https://letsencrypt.org/certs/lets-encrypt-e1.pem'
                    ];

                    foreach ($letsEncryptUrls as $url) {
                        $this->info("Baixando de: {$url}");
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        
                        $cert = curl_exec($ch);
                        curl_close($ch);
                        
                        if ($cert && $this->isValidCertificate($cert)) {
                            $bundle .= $cert . "\n";
                        }
                    }

                    // Salvar o bundle atualizado
                    file_put_contents($bundlePath, $bundle);
                    $this->info('Certificados Let\'s Encrypt adicionados ao bundle');
                }
            }

            fclose($socket);

            if (!$success) {
                throw new \Exception('Não foi possível extrair os certificados');
            }

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
        $certInfo = openssl_x509_parse($cert);

        $this->info("\nInformações do certificado:");
        $this->line("Subject: " . ($certInfo['subject']['CN'] ?? 'N/A'));
        $this->line("Issuer: " . ($certInfo['issuer']['CN'] ?? 'N/A'));
        $this->line("Valid From: " . date('Y-m-d H:i:s', $certInfo['validFrom_time_t']));
        $this->line("Valid To: " . date('Y-m-d H:i:s', $certInfo['validTo_time_t']));
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
