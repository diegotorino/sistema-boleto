<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Exception;
use App\Services\InterTokenService;

class InterBoletoService
{
    protected $client;
    protected $tokenService;
    protected $baseUrl;
    protected $certPath;
    protected $keyPath;
    protected $maxRetries = 3;
    protected $retryDelay = 1; // segundos

    public function __construct(InterTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
        $this->baseUrl = config('services.inter.base_url');
        $this->certPath = base_path('Sandbox_InterAPI_Certificado.crt');
        $this->keyPath = base_path('Sandbox_InterAPI_Chave.key');

        Log::info('InterBoletoService inicializado', [
            'baseUrl' => $this->baseUrl,
            'certPath' => $this->certPath,
            'keyPath' => $this->keyPath
        ]);

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'verify' => false,
            'cert' => $this->certPath,
            'ssl_key' => $this->keyPath
        ]);
    }

    protected function handleApiError($e)
    {
        if ($e instanceof ClientException) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            Log::error('Erro na API do Inter:', [
                'status' => $statusCode,
                'body' => $body
            ]);

            $message = $body['detail'] ?? $body['message'] ?? $body['title'] ?? 'Erro desconhecido';
            
            if (isset($body['violacoes']) && !empty($body['violacoes'])) {
                $message .= "\nViolações:\n";
                foreach ($body['violacoes'] as $violacao) {
                    $message .= "- " . ($violacao['mensagem'] ?? 'Violação sem mensagem') . "\n";
                }
            }

            return [
                'success' => false,
                'message' => $message,
                'status' => $statusCode,
                'errors' => $body
            ];
        }

        if ($e instanceof ServerException) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            Log::error('Erro no servidor do Inter:', [
                'status' => $statusCode,
                'body' => $body
            ]);

            return [
                'success' => false,
                'message' => $body['detail'] ?? $body['message'] ?? $body['title'] ?? 'Erro no servidor',
                'status' => $statusCode,
                'errors' => $body,
                'retry' => true
            ];
        }

        Log::error('Erro inesperado:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'success' => false,
            'message' => 'Erro inesperado: ' . $e->getMessage(),
            'status' => 500
        ];
    }

    protected function makeRequest($method, $endpoint, $options = [], $retryCount = 0)
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);
            return [
                'success' => true,
                'data' => json_decode($response->getBody(), true)
            ];
        } catch (\Exception $e) {
            $result = $this->handleApiError($e);

            // Se for erro 500 e ainda não atingimos o limite de tentativas, tenta novamente
            if (isset($result['retry']) && $result['retry'] && $retryCount < $this->maxRetries) {
                Log::info("Tentando novamente em {$this->retryDelay} segundos... (tentativa " . ($retryCount + 1) . " de {$this->maxRetries})");
                sleep($this->retryDelay);
                return $this->makeRequest($method, $endpoint, $options, $retryCount + 1);
            }

            return $result;
        }
    }

    public function createBoleto($data)
    {
        try {
            Log::info('Iniciando criação de boleto', ['data' => $data]);
            
            $token = $this->tokenService->getToken();
            Log::info('Token obtido com sucesso');

            return $this->makeRequest('POST', '/cobranca/v3/cobrancas', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'x-conta-corrente' => config('services.inter.conta_corrente')
                ],
                'json' => [
                    'seuNumero' => $data['seuNumero'],
                    'valorNominal' => $data['valorNominal'],
                    'dataVencimento' => $data['dataVencimento'],
                    'numDiasAgenda' => $data['numDiasAgenda'],
                    'pagador' => [
                        'cpfCnpj' => preg_replace('/[^0-9]/', '', $data['pagador']['cpfCnpj']),
                        'tipoPessoa' => $data['pagador']['tipoPessoa'],
                        'nome' => $data['pagador']['nome'],
                        'email' => $data['pagador']['email'],
                        'endereco' => $data['pagador']['endereco']['logradouro'],
                        'numero' => $data['pagador']['endereco']['numero'],
                        'complemento' => $data['pagador']['endereco']['complemento'],
                        'bairro' => $data['pagador']['endereco']['bairro'],
                        'cidade' => $data['pagador']['endereco']['cidade'],
                        'uf' => $data['pagador']['endereco']['uf'],
                        'cep' => preg_replace('/[^0-9]/', '', $data['pagador']['endereco']['cep'])
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return $this->handleApiError($e);
        }
    }

    public function getBoletoDetails($codigoSolicitacao)
    {
        try {
            Log::info('Buscando detalhes do boleto', ['codigoSolicitacao' => $codigoSolicitacao]);
            
            $token = $this->tokenService->getToken();
            Log::info('Token obtido com sucesso');

            $result = $this->makeRequest('GET', "/cobranca/v3/cobrancas/{$codigoSolicitacao}/pdf", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'x-conta-corrente' => config('services.inter.conta_corrente')
                ]
            ]);

            if (!$result['success']) {
                return $result;
            }

            if (!isset($result['data']['pdf'])) {
                throw new \Exception('PDF não encontrado na resposta');
            }

            $pdfContent = base64_decode($result['data']['pdf']);
            Log::info('PDF recebido', ['size' => strlen($pdfContent)]);

            $pdfPath = "boletos/{$codigoSolicitacao}.pdf";
            
            // Salva o PDF no storage
            Storage::disk('public')->put($pdfPath, $pdfContent);
            Log::info('PDF salvo com sucesso', ['path' => $pdfPath]);

            return [
                'success' => true,
                'data' => [
                    'pdf_path' => $pdfPath
                ]
            ];

        } catch (\Exception $e) {
            return $this->handleApiError($e);
        }
    }

    public function pagarBoleto($codigoSolicitacao)
    {
        try {
            $token = $this->tokenService->getToken();

            return $this->makeRequest('POST', "/cobranca/v3/cobrancas/{$codigoSolicitacao}/pagar", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'x-conta-corrente' => config('services.inter.conta_corrente')
                ],
                'json' => [
                    'tipoPagamento' => 'PIX'
                ]
            ]);

        } catch (\Exception $e) {
            return $this->handleApiError($e);
        }
    }

    public function cancelarBoleto($codigoSolicitacao, $motivo)
    {
        try {
            $token = $this->tokenService->getToken();

            return $this->makeRequest('POST', "/cobranca/v3/cobrancas/{$codigoSolicitacao}/cancelar", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'x-conta-corrente' => config('services.inter.conta_corrente')
                ],
                'json' => [
                    'motivoCancelamento' => substr($motivo, 0, 50)
                ]
            ]);

        } catch (\Exception $e) {
            return $this->handleApiError($e);
        }
    }
}
