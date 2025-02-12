<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BaixarCertificadoCA extends Command
{
    protected $signature = 'cert:baixar-ca-direto';
    protected $description = 'Baixa o certificado CA diretamente do servidor do Banco Inter';

    public function handle()
    {
        $this->info('Baixando certificado CA do servidor do Banco Inter...');

        $host = 'cdpj.partners.bancointer.com.br';
        $port = 443;

        // Criar um contexto SSL sem verificação
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'capture_peer_cert' => true
            ]
        ]);

        // Conectar ao servidor
        $this->info("Conectando ao servidor $host:$port...");
        $socket = stream_socket_client(
            "ssl://$host:$port",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            $this->error("Erro ao conectar: $errstr ($errno)");
            return 1;
        }

        // Obter informações do contexto
        $params = stream_context_get_params($socket);
        
        if (!isset($params['options']['ssl']['peer_certificate'])) {
            $this->error('Não foi possível obter o certificado do servidor');
            return 1;
        }

        // Extrair o certificado
        $cert = $params['options']['ssl']['peer_certificate'];
        
        // Converter para PEM
        $certData = '';
        openssl_x509_export($cert, $certData);

        // Salvar o certificado
        $caPath = storage_path('app/private/inter_ca.crt');
        file_put_contents($caPath, $certData);

        $this->info("Certificado CA salvo em: $caPath");

        // Exibir informações do certificado
        $certInfo = openssl_x509_parse($cert);
        $this->info("\nInformações do certificado:");
        $this->line("Subject: " . $certInfo['subject']['CN']);
        $this->line("Issuer: " . $certInfo['issuer']['CN']);
        $this->line("Valid From: " . date('Y-m-d H:i:s', $certInfo['validFrom_time_t']));
        $this->line("Valid To: " . date('Y-m-d H:i:s', $certInfo['validTo_time_t']));

        // Fechar a conexão
        fclose($socket);

        return 0;
    }
}
