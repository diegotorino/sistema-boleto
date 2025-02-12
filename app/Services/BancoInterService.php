<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class BancoInterService
{
    protected $client;
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $certificatePath;
    protected $certificateKey;

    public function __construct()
    {
        $this->baseUrl = config('services.banco_inter.base_url', 'https://cdpj.partners.bancointer.com.br');
        $this->clientId = config('services.banco_inter.client_id');
        $this->clientSecret = config('services.banco_inter.client_secret');
        $this->certificatePath = config('services.banco_inter.certificate_path');
        $this->certificateKey = config('services.banco_inter.certificate_key');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'verify' => false, // Em produção, isso deve ser true
            'cert' => $this->certificatePath,
            'ssl_key' => $this->certificateKey,
        ]);
    }

    public function getAccessToken()
    {
        try {
            $response = $this->client->post('/oauth/v2/token', [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials',
                    'scope' => 'boleto-cobranca.read boleto-cobranca.write'
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['access_token'];
        } catch (\Exception $e) {
            Log::error('Erro ao obter token do Banco Inter: ' . $e->getMessage());
            throw $e;
        }
    }

    public function gerarBoleto($dados)
    {
        try {
            $token = $this->getAccessToken();

            $response = $this->client->post('/cobranca/v2/boletos', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'seuNumero' => $dados['nosso_numero'],
                    'valorNominal' => $dados['valor'],
                    'dataVencimento' => $dados['vencimento'],
                    'numDiasAgenda' => 60,
                    'pagador' => [
                        'cpfCnpj' => preg_replace('/\D/', '', $dados['pagador_cpf_cnpj']),
                        'nome' => $dados['pagador_nome'],
                        'endereco' => $dados['pagador_endereco'],
                        'cidade' => $dados['pagador_cidade'],
                        'uf' => $dados['pagador_estado'],
                        'cep' => preg_replace('/\D/', '', $dados['pagador_cep']),
                    ],
                    'mensagem' => [
                        'linha1' => 'Referente à cobrança de serviços'
                    ]
                ]
            ]);

            $boleto = json_decode($response->getBody(), true);
            
            return [
                'nosso_numero' => $boleto['nossoNumero'],
                'linha_digitavel' => $boleto['linhaDigitavel'],
                'codigo_barras' => $boleto['codigoBarras'],
                'pdf_url' => $this->getPdfBoleto($boleto['nossoNumero'])
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao gerar boleto no Banco Inter: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getPdfBoleto($nossoNumero)
    {
        try {
            $token = $this->getAccessToken();

            $response = $this->client->get("/cobranca/v2/boletos/{$nossoNumero}/pdf", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            // Salva o PDF em um diretório público
            $pdfPath = "boletos/{$nossoNumero}.pdf";
            $fullPath = storage_path("app/public/{$pdfPath}");
            
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
            
            file_put_contents($fullPath, $response->getBody());
            
            return asset("storage/{$pdfPath}");
        } catch (\Exception $e) {
            Log::error('Erro ao obter PDF do boleto: ' . $e->getMessage());
            throw $e;
        }
    }
}
