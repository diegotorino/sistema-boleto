<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DownloadCA extends Command
{
    protected $signature = 'cert:download';
    protected $description = 'Baixa o certificado CA do Banco Inter';

    public function handle()
    {
        $this->info('Baixando certificado CA do Banco Inter...');

        $url = 'cdpj.partners.bancointer.com.br';
        $caPath = storage_path('app/private/inter_ca.crt');

        // Criar contexto SSL
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]);

        $this->info('Conectando ao servidor...');

        // Conectar ao servidor
        $socket = stream_socket_client(
            "ssl://{$url}:443",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            $this->error("Erro ao conectar: $errstr ($errno)");
            return;
        }

        $this->info('Obtendo certificados...');

        // Obter certificados
        $params = stream_context_get_params($socket);
        
        if (!empty($params['options']['ssl']['peer_certificate'])) {
            $this->info('Certificado obtido com sucesso!');
            
            // Exportar certificado
            $cert = $params['options']['ssl']['peer_certificate'];
            openssl_x509_export($cert, $certData);
            
            // Salvar certificado
            file_put_contents($caPath, $certData);
            
            $this->info('Certificado salvo em: ' . $caPath);
            
            // Exibir informações do certificado
            $certInfo = openssl_x509_parse($cert);
            $this->info('Informações do certificado:');
            $this->line('Subject: ' . $certInfo['subject']['CN']);
            $this->line('Issuer: ' . $certInfo['issuer']['CN']);
            $this->line('Valid From: ' . date('Y-m-d H:i:s', $certInfo['validFrom_time_t']));
            $this->line('Valid To: ' . date('Y-m-d H:i:s', $certInfo['validTo_time_t']));
        } else {
            $this->error('Não foi possível obter o certificado');
        }

        fclose($socket);
    }
}
