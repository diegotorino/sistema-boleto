<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConvertCertificate extends Command
{
    protected $signature = 'cert:convert';
    protected $description = 'Converte o certificado para o formato PEM correto';

    public function handle()
    {
        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');
        $pemPath = storage_path('app/private/Sandbox_InterAPI_Final.pem');

        $this->info('Convertendo certificados...');

        // Ler o certificado e a chave
        $cert = file_get_contents($certPath);
        $key = file_get_contents($keyPath);

        // Remover qualquer espaço em branco ou quebra de linha extra
        $cert = trim($cert);
        $key = trim($key);

        // Garantir que temos as tags corretas
        if (!str_starts_with($cert, '-----BEGIN CERTIFICATE-----')) {
            $cert = "-----BEGIN CERTIFICATE-----\n" . $cert;
        }
        if (!str_ends_with($cert, '-----END CERTIFICATE-----')) {
            $cert .= "\n-----END CERTIFICATE-----";
        }

        if (!str_starts_with($key, '-----BEGIN PRIVATE KEY-----')) {
            $key = "-----BEGIN PRIVATE KEY-----\n" . $key;
        }
        if (!str_ends_with($key, '-----END PRIVATE KEY-----')) {
            $key .= "\n-----END PRIVATE KEY-----";
        }

        // Adicionar quebras de linha a cada 64 caracteres
        $cert = chunk_split(base64_encode(base64_decode(str_replace([
            '-----BEGIN CERTIFICATE-----',
            '-----END CERTIFICATE-----',
            "\n",
            "\r"
        ], '', $cert))), 64);

        $key = chunk_split(base64_encode(base64_decode(str_replace([
            '-----BEGIN PRIVATE KEY-----',
            '-----END PRIVATE KEY-----',
            "\n",
            "\r"
        ], '', $key))), 64);

        // Montar o arquivo PEM final
        $pem = "-----BEGIN CERTIFICATE-----\n";
        $pem .= $cert;
        $pem .= "-----END CERTIFICATE-----\n";
        $pem .= "-----BEGIN PRIVATE KEY-----\n";
        $pem .= $key;
        $pem .= "-----END PRIVATE KEY-----\n";

        // Salvar o arquivo PEM
        file_put_contents($pemPath, $pem);

        $this->info('Certificado convertido com sucesso!');
        $this->info('Arquivo gerado: ' . $pemPath);

        // Exibir o conteúdo para verificação
        $this->info('Conteúdo do arquivo PEM:');
        $this->line(file_get_contents($pemPath));
    }
}
