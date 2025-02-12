<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class InterService
{
    private $baseUrl;
    private $clientId;
    private $clientSecret;
    private $accessToken;
    private $tokenExpiresAt;
    private $certPath;
    private $keyPath;
    private $caPath;

    public function __construct()
    {
        $this->baseUrl = config('services.inter.url');
        $this->clientId = config('services.inter.client_id');
        $this->clientSecret = config('services.inter.client_secret');
        
        $this->certPath = storage_path(config('services.inter.cert_path'));
        $this->keyPath = storage_path(config('services.inter.key_path'));
        $this->caPath = storage_path(config('services.inter.ca_path'));

        Log::info('Caminhos dos certificados:', [
            'cert' => $this->certPath,
            'key' => $this->keyPath,
            'ca' => $this->caPath
        ]);

        if (!file_exists($this->certPath)) {
            throw new Exception("Certificado não encontrado em: " . $this->certPath);
        }
        if (!file_exists($this->keyPath)) {
            throw new Exception("Chave privada não encontrada em: " . $this->keyPath);
        }
        if (!file_exists($this->caPath)) {
            throw new Exception("Certificado CA não encontrado em: " . $this->caPath);
        }

        // Convertendo caminhos para o formato Windows
        $this->certPath = str_replace('/', '\\', $this->certPath);
        $this->keyPath = str_replace('/', '\\', $this->keyPath);
        $this->caPath = str_replace('/', '\\', $this->caPath);
        
        // Inicializa o token como nulo
        $this->accessToken = null;
        $this->tokenExpiresAt = null;
    }

    private function tokenIsValid()
    {
        if (!$this->accessToken || !$this->tokenExpiresAt) {
            return false;
        }

        // Adiciona uma margem de segurança de 30 segundos
        return $this->tokenExpiresAt > time() + 30;
    }

    private function handleErrorResponse($statusCode, $response)
    {
        switch ($statusCode) {
            case 400:
                throw new Exception("Requisição com formato inválido: " . $response);
            case 401:
                throw new Exception("Requisição não autorizada");
            case 403:
                throw new Exception("Violação de regra de autorização");
            case 404:
                throw new Exception("Recurso não encontrado");
            case 503:
                throw new Exception("Serviço não está disponível no momento");
            default:
                if ($statusCode >= 400) {
                    throw new Exception("Erro desconhecido: HTTP $statusCode - " . $response);
                }
        }
    }

    private function makeRequest($method, $endpoint, $data = [], $headers = [])
    {
        $ch = curl_init();
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLCERT, $this->certPath);
        curl_setopt($ch, CURLOPT_SSLKEY, $this->keyPath);
        curl_setopt($ch, CURLOPT_CAINFO, $this->caPath);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $headers = array_merge([
            'Accept: application/json',
            'Content-Type: application/json',
        ], $headers);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? http_build_query($data) : $data);
            }
        }

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);

        if ($error) {
            throw new Exception("Erro cURL: " . $error);
        }

        return [
            'statusCode' => $statusCode,
            'body' => $response
        ];
    }

    private function getAccessToken()
    {
        if ($this->tokenIsValid()) {
            return $this->accessToken;
        }

        try {
            Log::info('Obtendo novo token de acesso');

            $data = [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => 'boleto-cobranca.read boleto-cobranca.write',
                'grant_type' => 'client_credentials'
            ];

            $headers = [
                'Content-Type: application/x-www-form-urlencoded'
            ];

            $response = $this->makeRequest('POST', 'oauth/v2/token', $data, $headers);

            Log::info('Resposta da requisição de token', [
                'statusCode' => $response['statusCode'],
                'body' => $response['body']
            ]);

            $this->handleErrorResponse($response['statusCode'], $response['body']);

            $data = json_decode($response['body'], true);
            if (!isset($data['access_token'])) {
                throw new Exception('Token não encontrado na resposta: ' . $response['body']);
            }

            $this->accessToken = $data['access_token'];
            $this->tokenExpiresAt = time() + ($data['expires_in'] ?? 600);
            
            Log::info('Token obtido com sucesso', [
                'expiresAt' => date('Y-m-d H:i:s', $this->tokenExpiresAt),
                'tokenType' => $data['token_type'] ?? 'unknown',
                'scope' => $data['scope'] ?? 'unknown'
            ]);

            return $this->accessToken;

        } catch (Exception $e) {
            Log::error('Erro ao obter token do Banco Inter', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Erro ao obter token de acesso: ' . $e->getMessage());
        }
    }

    public function createBoleto(array $data)
    {
        try {
            $token = $this->getAccessToken();
            Log::info('Criando boleto', $data);

            $headers = [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ];

            $response = $this->makeRequest('POST', 'cobranca/v2/boletos', json_encode($data), $headers);

            $this->handleErrorResponse($response['statusCode'], $response['body']);

            $result = json_decode($response['body'], true);
            Log::info('Boleto criado com sucesso', $result);

            return $result;

        } catch (Exception $e) {
            Log::error('Erro ao criar boleto no Banco Inter', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Erro ao criar boleto: ' . $e->getMessage());
        }
    }
}
