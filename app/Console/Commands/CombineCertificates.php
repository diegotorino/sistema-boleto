<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CombineCertificates extends Command
{
    protected $signature = 'cert:combine';
    protected $description = 'Combina o certificado e a chave em um único arquivo PEM';

    public function handle()
    {
        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');
        $combinedPath = storage_path('app/private/Sandbox_InterAPI_Combined.pem');

        $cert = file_get_contents($certPath);
        $key = file_get_contents($keyPath);

        $combined = $cert . "\n" . $key;

        file_put_contents($combinedPath, $combined);

        $this->info('Certificados combinados com sucesso!');
        $this->info('Arquivo gerado: ' . $combinedPath);
    }
}
