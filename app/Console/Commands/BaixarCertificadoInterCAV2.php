<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BaixarCertificadoInterCAV2 extends Command
{
    protected $signature = 'cert:baixar-inter-ca-v2';
    protected $description = 'Baixa o certificado CA do site do Inter (V2)';

    private $urls = [
        'https://cdpj.partners.bancointer.com.br',
        'https://api.bancointer.com.br',
        'https://apis.bancointer.com.br',
        'https://apis.stage.bancointer.com.br',
        'https://apis.hml.bancointer.com.br',
        'https://apis.dev.bancointer.com.br'
    ];

    public function handle()
    {
        $this->info('Baixando certificado CA do Inter (V2)...');

        try {
            $success = false;

            foreach ($this->urls as $url) {
                $this->info("\nTentando extrair certificado de: $url");
                
                try {
                    // Criar contexto SSL sem verificação
                    $context = stream_context_create([
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'capture_peer_cert_chain' => true
                        ]
                    ]);

                    $client = stream_socket_client(
                        "ssl://" . parse_url($url, PHP_URL_HOST) . ":443",
                        $errno,
                        $errstr,
                        30,
                        STREAM_CLIENT_CONNECT,
                        $context
                    );

                    if ($client) {
                        $params = stream_context_get_params($client);
                        
                        if (isset($params['options']['ssl']['peer_certificate_chain'])) {
                            $certChain = $params['options']['ssl']['peer_certificate_chain'];
                            
                            // Extrair todos os certificados da cadeia
                            foreach ($certChain as $index => $cert) {
                                $certData = '';
                                openssl_x509_export($cert, $certData);
                                
                                // Salvar cada certificado
                                $certPath = storage_path("app/private/inter_ca_{$index}.crt");
                                file_put_contents($certPath, $certData);
                                
                                // Exibir informações do certificado
                                $this->displayCertificateInfo($cert);
                                
                                $success = true;
                            }

                            // Criar um bundle com todos os certificados
                            if ($success) {
                                $bundle = '';
                                for ($i = 0; $i < count($certChain); $i++) {
                                    $certPath = storage_path("app/private/inter_ca_{$i}.crt");
                                    $bundle .= file_get_contents($certPath) . "\n";
                                }

                                // Salvar o bundle
                                $bundlePath = storage_path('app/private/inter_ca.crt');
                                file_put_contents($bundlePath, $bundle);
                                
                                $this->info("\nBundle de certificados criado em: $bundlePath");
                            }
                        }
                        
                        fclose($client);
                        
                        if ($success) {
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    $this->warn("Erro ao tentar $url: " . $e->getMessage());
                }
            }

            if (!$success) {
                $this->error('Não foi possível extrair os certificados de nenhuma URL.');
                return 1;
            }

            // Configurar o PHP para usar o novo certificado
            $this->info("\nConfigurando PHP para usar o novo certificado...");
            
            $bundlePath = storage_path('app/private/inter_ca.crt');
            
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

            return 0;

        } catch (\Exception $e) {
            $this->error('Erro: ' . $e->getMessage());
            return 1;
        }
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
