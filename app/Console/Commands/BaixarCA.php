<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class BaixarCA extends Command
{
    protected $signature = 'cert:baixar-ca';
    protected $description = 'Baixa o certificado CA do Banco Inter';

    public function handle()
    {
        $this->info('Baixando certificado CA do Banco Inter...');

        try {
            // URL do certificado CA
            $url = 'https://cdpj.partners.bancointer.com.br/doc/security/INTER_CA.crt';
            
            // Diretório onde o certificado será salvo
            $certDir = storage_path('app/private');
            if (!file_exists($certDir)) {
                mkdir($certDir, 0777, true);
            }

            // Caminho completo do arquivo
            $caPath = storage_path('app/private/inter_ca.crt');

            // Baixar o certificado
            $response = Http::withoutVerifying()->get($url);
            
            if ($response->successful()) {
                file_put_contents($caPath, $response->body());
                $this->info('Certificado CA baixado com sucesso para: ' . $caPath);

                // Verificar se é um certificado válido
                $cert = openssl_x509_read(file_get_contents($caPath));
                if (!$cert) {
                    $this->error('O arquivo baixado não é um certificado válido!');
                    return 1;
                }

                // Exibir informações do certificado
                $certInfo = openssl_x509_parse($cert);
                $this->info('Informações do certificado CA:');
                $this->line('Subject: ' . $certInfo['subject']['CN']);
                $this->line('Issuer: ' . $certInfo['issuer']['CN']);
                $this->line('Valid From: ' . date('Y-m-d H:i:s', $certInfo['validFrom_time_t']));
                $this->line('Valid To: ' . date('Y-m-d H:i:s', $certInfo['validTo_time_t']));

                // Limpar recursos
                openssl_x509_free($cert);

                return 0;
            } else {
                $this->error('Erro ao baixar o certificado CA: ' . $response->status());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Erro: ' . $e->getMessage());
            return 1;
        }
    }
}
