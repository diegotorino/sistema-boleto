<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class TestInterConnection extends Command
{
    protected $signature = 'test:inter-connection';
    protected $description = 'Testa a conexão com o Banco Inter';

    public function handle()
    {
        $this->info('Testando conexão com o Banco Inter...');

        try {
            $client = new Client([
                'verify' => config('guzzle.verify'),
                'cert' => config('guzzle.cert'),
                'ssl_key' => config('guzzle.ssl_key'),
                'debug' => true,
            ]);

            $this->info('Tentando obter token...');

            $response = $client->post('https://cdpj.partners.bancointer.com.br/oauth/v2/token', [
                'form_params' => [
                    'client_id' => config('services.banco_inter.client_id'),
                    'client_secret' => config('services.banco_inter.client_secret'),
                    'grant_type' => 'client_credentials',
                    'scope' => 'boleto-cobranca.write boleto-cobranca.read',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            $this->info('Resposta do servidor:');
            $this->line(json_encode($data, JSON_PRETTY_PRINT));

            if (isset($data['access_token'])) {
                $this->info('Token obtido com sucesso!');
            } else {
                $this->error('Token não encontrado na resposta.');
            }

        } catch (RequestException $e) {
            $this->error('Erro na requisição:');
            if ($e->hasResponse()) {
                $this->error($e->getResponse()->getBody());
            } else {
                $this->error($e->getMessage());
            }
            
            $this->info('Detalhes do erro:');
            $this->line('Request: ' . $e->getRequest()->getMethod() . ' ' . $e->getRequest()->getUri());
            $this->line('Headers:');
            foreach ($e->getRequest()->getHeaders() as $name => $values) {
                $this->line($name . ': ' . implode(', ', $values));
            }
            
            if ($e->hasResponse()) {
                $this->line('Response Status: ' . $e->getResponse()->getStatusCode());
                $this->line('Response Body: ' . $e->getResponse()->getBody());
            }
        } catch (\Exception $e) {
            $this->error('Erro geral:');
            $this->error($e->getMessage());
            $this->line($e->getTraceAsString());
        }
    }
}
