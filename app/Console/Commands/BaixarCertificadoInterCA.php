<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class BaixarCertificadoInterCA extends Command
{
    protected $signature = 'cert:baixar-inter-ca';
    protected $description = 'Baixa o certificado CA do site do Inter';

    public function handle()
    {
        $this->info('Baixando certificado CA do site do Inter...');

        try {
            // URLs para tentar baixar o certificado
            $urls = [
                'https://cdpj.partners.bancointer.com.br/doc/security/INTER_CA.crt',
                'https://cdpj.partners.bancointer.com.br/INTER_CA.crt',
                'https://cdpj.partners.bancointer.com.br/ca.crt',
                'https://cdpj.partners.bancointer.com.br/ca-bundle.crt'
            ];

            $success = false;

            foreach ($urls as $url) {
                $this->info("Tentando baixar de: $url");

                try {
                    $response = Http::withoutVerifying()->get($url);
                    
                    if ($response->successful()) {
                        $cert = $response->body();
                        
                        // Verificar se é um certificado válido
                        if ($this->isValidCertificate($cert)) {
                            // Salvar o certificado
                            $caPath = storage_path('app/private/inter_ca.crt');
                            file_put_contents($caPath, $cert);
                            
                            $this->info("Certificado salvo com sucesso em: $caPath");
                            $this->displayCertificateInfo($cert);
                            
                            $success = true;
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    $this->warn("Erro ao tentar $url: " . $e->getMessage());
                }
            }

            if (!$success) {
                $this->error('Não foi possível baixar o certificado CA de nenhuma URL.');
                
                // Tentar extrair o certificado do servidor
                $this->info("\nTentando extrair certificado diretamente do servidor...");
                
                $host = 'cdpj.partners.bancointer.com.br';
                $port = 443;
                
                $context = stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'capture_peer_cert' => true
                    ]
                ]);

                $socket = @stream_socket_client(
                    "ssl://$host:$port",
                    $errno,
                    $errstr,
                    30,
                    STREAM_CLIENT_CONNECT,
                    $context
                );

                if ($socket) {
                    $params = stream_context_get_params($socket);
                    
                    if (isset($params['options']['ssl']['peer_certificate'])) {
                        $cert = $params['options']['ssl']['peer_certificate'];
                        $certData = '';
                        openssl_x509_export($cert, $certData);
                        
                        $caPath = storage_path('app/private/inter_ca.crt');
                        file_put_contents($caPath, $certData);
                        
                        $this->info("Certificado extraído do servidor e salvo em: $caPath");
                        $this->displayCertificateInfo($certData);
                        
                        $success = true;
                    }
                    
                    fclose($socket);
                }
            }

            if (!$success) {
                return 1;
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
        $certResource = openssl_x509_read($cert);
        $certInfo = openssl_x509_parse($certResource);

        $this->info("\nInformações do certificado:");
        $this->line("Subject: " . $certInfo['subject']['CN']);
        $this->line("Issuer: " . $certInfo['issuer']['CN']);
        $this->line("Valid From: " . date('Y-m-d H:i:s', $certInfo['validFrom_time_t']));
        $this->line("Valid To: " . date('Y-m-d H:i:s', $certInfo['validTo_time_t']));

        openssl_x509_free($certResource);
    }
}
