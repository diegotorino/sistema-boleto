<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class InterService
{
    protected $client;
    protected $baseUrl;
    protected $certificatePath;
    protected $keyPath;
    protected $scope = 'boleto-cobranca.read boleto-cobranca.write';
    protected $clientId;
    protected $clientSecret;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.inter.base_url', 'https://cdpj.partners.bancointer.com.br');
        
        // Caminhos absolutos para os certificados
        $this->certificatePath = storage_path('app/certificates/Sandbox_InterAPI_Certificado.crt');
        $this->keyPath = storage_path('app/certificates/Sandbox_InterAPI_Chave.key');
        
        // Verifica se os certificados existem
        if (!file_exists($this->certificatePath)) {
            throw new Exception("Certificado não encontrado em: " . $this->certificatePath);
        }
        if (!file_exists($this->keyPath)) {
            throw new Exception("Chave privada não encontrada em: " . $this->keyPath);
        }

        $this->clientId = config('services.inter.client_id');
        $this->clientSecret = config('services.inter.client_secret');

        // Configuração do cliente HTTP
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'verify' => false, // Desativa verificação SSL para ambiente de desenvolvimento
            'cert' => $this->certificatePath,
            'ssl_key' => $this->keyPath,
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);

        Log::info('InterService inicializado', [
            'baseUrl' => $this->baseUrl,
            'certificatePath' => $this->certificatePath,
            'keyPath' => $this->keyPath
        ]);
    }

    protected function getToken()
    {
        try {
            if ($this->token && $this->token['expires_at'] > now()) {
                return $this->token['access_token'];
            }

            Log::info('Obtendo novo token do Banco Inter');

            $response = $this->client->post('/oauth/v2/token', [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope' => $this->scope,
                    'grant_type' => 'client_credentials'
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (!isset($data['access_token'])) {
                throw new Exception('Token não encontrado na resposta do Banco Inter');
            }

            $this->token = [
                'access_token' => $data['access_token'],
                'expires_at' => now()->addSeconds($data['expires_in'])
            ];

            Log::info('Token obtido com sucesso', [
                'expires_at' => $this->token['expires_at']
            ]);

            return $this->token['access_token'];
        } catch (Exception $e) {
            Log::error('Erro ao obter token do Banco Inter', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function createBoleto($data)
    {
        try {
            $token = $this->getToken();

            Log::info('Criando boleto no Banco Inter', $data);

            $response = $this->client->post('/cobranca/v2/boletos', [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'seuNumero' => $data['seuNumero'],
                    'valorNominal' => $data['valorNominal'],
                    'dataVencimento' => Carbon::parse($data['dataVencimento'])->format('Y-m-d'),
                    'numDiasAgenda' => 60,
                    'pagador' => [
                        'cpfCnpj' => preg_replace('/[^0-9]/', '', $data['pagador']['cpfCnpj']),
                        'tipoPessoa' => strlen(preg_replace('/[^0-9]/', '', $data['pagador']['cpfCnpj'])) > 11 ? 'JURIDICA' : 'FISICA',
                        'nome' => $data['pagador']['nome'],
                        'endereco' => $data['pagador']['endereco'],
                        'cidade' => $data['pagador']['cidade'] ?? 'São Paulo',
                        'uf' => $data['pagador']['uf'] ?? 'SP',
                        'cep' => preg_replace('/[^0-9]/', '', $data['pagador']['cep'] ?? '01000000')
                    ],
                    'mensagem' => $data['mensagem'] ?? null
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info('Boleto criado com sucesso', $result);

            return $result;
        } catch (Exception $e) {
            Log::error('Erro ao criar boleto no Banco Inter', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function getPdf($nossoNumero)
    {
        try {
            $token = $this->getToken();

            Log::info('Obtendo PDF do boleto', ['nossoNumero' => $nossoNumero]);

            $response = $this->client->get("/cobranca/v2/boletos/{$nossoNumero}/pdf", [
                'headers' => [
                    'Authorization' => "Bearer {$token}"
                ]
            ]);

            return $response->getBody();
        } catch (Exception $e) {
            Log::error('Erro ao obter PDF do boleto', [
                'nossoNumero' => $nossoNumero,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
