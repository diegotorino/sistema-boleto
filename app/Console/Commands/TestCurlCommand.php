<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCurlCommand extends Command
{
    protected $signature = 'test:curl-command';
    protected $description = 'Teste usando curl na linha de comando';

    public function handle()
    {
        $this->info('Testando com curl na linha de comando...');

        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');
        $caPath = storage_path('app/private/inter_ca.crt');

        $url = 'https://cdpj.partners.bancointer.com.br/oauth/v2/token';
        $clientId = config('services.banco_inter.client_id');
        $clientSecret = config('services.banco_inter.client_secret');

        $postData = http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => 'boleto-cobranca.write boleto-cobranca.read'
        ]);

        // Montar o comando curl
        $command = sprintf(
            'curl -v -X POST "%s" ' .
            '--cert "%s" ' .
            '--key "%s" ' .
            '--cacert "%s" ' .
            '--tlsv1.2 ' .
            '-H "Content-Type: application/x-www-form-urlencoded" ' .
            '-d "%s"',
            $url,
            $certPath,
            $keyPath,
            $caPath,
            $postData
        );

        $this->info('Executando comando:');
        $this->line($command);

        // Executar o comando
        $output = [];
        $returnVar = 0;
        exec($command . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Erro ao executar o comando (código ' . $returnVar . '):');
            foreach ($output as $line) {
                $this->error($line);
            }
        } else {
            $this->info('Resposta do servidor:');
            foreach ($output as $line) {
                $this->line($line);
            }
        }
    }
}
