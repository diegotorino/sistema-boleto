<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class TestInterConfig extends Command
{
    protected $signature = 'test:inter-config';
    protected $description = 'Testa as configurações do Banco Inter';

    public function handle()
    {
        $this->info('Testando configurações do Banco Inter...');
        
        $this->info('Client ID: ' . Config::get('services.banco_inter.client_id'));
        $this->info('Certificate Path: ' . Config::get('services.banco_inter.certificate_path'));
        $this->info('Certificate Key: ' . Config::get('services.banco_inter.certificate_key'));
        
        // Testa se os arquivos existem
        $certPath = Config::get('services.banco_inter.certificate_path');
        $keyPath = Config::get('services.banco_inter.certificate_key');
        
        $this->info('Certificado existe: ' . (file_exists($certPath) ? 'Sim' : 'Não'));
        $this->info('Chave existe: ' . (file_exists($keyPath) ? 'Sim' : 'Não'));
        
        // Tenta ler os arquivos
        $this->info('Conteúdo do certificado: ' . (file_get_contents($certPath) ? 'Lido com sucesso' : 'Erro ao ler'));
        $this->info('Conteúdo da chave: ' . (file_get_contents($keyPath) ? 'Lido com sucesso' : 'Erro ao ler'));
    }
}
