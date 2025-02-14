<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class InterTokenService
{
    protected $client;
    protected $baseUrl;
    protected $certPath;
    protected $keyPath;

    public function __construct()
    {
        $this->baseUrl = config('services.inter.base_url');
        $this->certPath = base_path('Sandbox_InterAPI_Certificado.crt');
        $this->keyPath = base_path('Sandbox_InterAPI_Chave.key');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'verify' => false, // Desabilita verificação SSL temporariamente
            'cert' => $this->certPath,
            'ssl_key' => $this->keyPath,
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
    }

    public function getAccessToken()
    {
        try {
            Log::info('Obtendo novo token de acesso');

            $response = $this->client->post('/oauth/v2/token', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => [
                    'client_id' => config('services.inter.client_id'),
                    'client_secret' => config('services.inter.client_secret'),
                    'grant_type' => 'client_credentials',
                    'scope' => 'boleto-cobranca.write boleto-cobranca.read'
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (!isset($result['access_token'])) {
                throw new \Exception('Token não encontrado na resposta');
            }

            Log::info('Token obtido com sucesso');

            return $result['access_token'];

        } catch (\Exception $e) {
            Log::error('Erro ao obter token de acesso', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    public function getToken()
    {
        try {
            // Tentar obter token do cache
            $token = Cache::get('inter_token');

            if (!$token) {
                Log::info('Token expirado ou não encontrado. Obtendo novo token de acesso');
                
                // Se não existe no cache, gera um novo
                $token = $this->getAccessToken();
                
                Log::info('Token obtido com sucesso. Expira em 10 minutos.');
                
                // Armazena no cache por 10 minutos (o token dura 1 hora, mas vamos renovar antes)
                Cache::put('inter_token', $token, now()->addMinutes(10));
            }

            return $token;
        } catch (\Exception $e) {
            Log::error('Erro ao obter/gerar token:', [
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
