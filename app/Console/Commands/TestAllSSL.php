<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestAllSSL extends Command
{
    protected $signature = 'test:allssl';
    protected $description = 'Testa todas as configurações SSL possíveis';

    private function testConfig($ch, $config)
    {
        $this->info("\nTestando configuração:");
        $this->line(json_encode($config, JSON_PRETTY_PRINT));

        foreach ($config as $option => $value) {
            curl_setopt($ch, $option, $value);
        }

        $response = curl_exec($ch);
        
        if ($response === false) {
            $this->error('Erro:');
            $this->error(curl_error($ch));
            $this->error('Código: ' . curl_errno($ch));
            return false;
        } else {
            $this->info('Sucesso!');
            $this->line($response);
            return true;
        }
    }

    public function handle()
    {
        $this->info('Iniciando testes de SSL...');

        $url = 'https://cdpj.partners.bancointer.com.br/oauth/v2/token';
        $certPath = storage_path('app/private/Sandbox_InterAPI_Certificado.crt');
        $keyPath = storage_path('app/private/Sandbox_InterAPI_Chave.key');
        $pemPath = storage_path('app/private/Sandbox_InterAPI_New.pem');

        $clientId = config('services.banco_inter.client_id');
        $clientSecret = config('services.banco_inter.client_secret');

        $postData = http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => 'boleto-cobranca.write boleto-cobranca.read'
        ]);

        // Configurações base que serão usadas em todos os testes
        $baseConfig = [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ];

        // Diferentes configurações SSL para testar
        $sslConfigs = [
            // Teste 1: Certificado e chave separados
            [
                CURLOPT_SSLCERT => $certPath,
                CURLOPT_SSLKEY => $keyPath,
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            ],
            
            // Teste 2: Certificado PEM combinado
            [
                CURLOPT_SSLCERT => $pemPath,
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            ],
            
            // Teste 3: Sem verificação SSL
            [
                CURLOPT_SSLCERT => $certPath,
                CURLOPT_SSLKEY => $keyPath,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            ],
            
            // Teste 4: TLS 1.1
            [
                CURLOPT_SSLCERT => $certPath,
                CURLOPT_SSLKEY => $keyPath,
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_1,
            ],
            
            // Teste 5: Sem versão SSL específica
            [
                CURLOPT_SSLCERT => $certPath,
                CURLOPT_SSLKEY => $keyPath,
            ],
            
            // Teste 6: Certificado PEM + Sem verificação
            [
                CURLOPT_SSLCERT => $pemPath,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            ]
        ];

        foreach ($sslConfigs as $index => $sslConfig) {
            $this->info("\n=== Teste " . ($index + 1) . " ===");
            
            $ch = curl_init();
            
            // Aplicar configurações base
            foreach ($baseConfig as $option => $value) {
                curl_setopt($ch, $option, $value);
            }
            
            // Testar configuração SSL
            if ($this->testConfig($ch, $sslConfig)) {
                $this->info('Configuração ' . ($index + 1) . ' funcionou!');
                break;
            }
            
            curl_close($ch);
        }
    }
}
