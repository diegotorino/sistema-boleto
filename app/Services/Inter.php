<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Inter
{
    private $certificado;
    private $chavePrivada;
    private $clientId;
    private $clientSecret;
    private $contaCorrente;
    private $ambiente;
    private $baseUrl;
    private $token;

    public function __construct(array $config)
    {
        $this->certificado = $config['certificado'];
        $this->chavePrivada = $config['chavePrivada'];
        $this->clientId = $config['clientId'];
        $this->clientSecret = $config['clientSecret'];
        $this->contaCorrente = $config['contaCorrente'];
        $this->ambiente = $config['ambiente'] ?? 'producao';
        $this->baseUrl = $this->ambiente === 'homologacao' 
            ? 'https://cdpj.partners.bancointer.com.br'
            : 'https://apis.bancointer.com.br';
    }

    private function getClient()
    {
        return new Client([
            'verify' => false,
            'cert' => $this->certificado,
            'ssl_key' => $this->chavePrivada,
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
    }

    private function getToken()
    {
        if ($this->token) {
            return $this->token;
        }

        try {
            $client = $this->getClient();

            $response = $client->post("{$this->baseUrl}/oauth/v2/token", [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials',
                    'scope' => 'boleto-cobranca.write boleto-cobranca.read'
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $this->token = $data['access_token'];
            return $this->token;

        } catch (GuzzleException $e) {
            throw new Exception("Erro ao obter token: " . $e->getMessage());
        }
    }

    private function request($method, $endpoint, $data = null)
    {
        try {
            $token = $this->getToken();
            $client = $this->getClient();

            $options = [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'x-conta-corrente' => $this->contaCorrente
                ]
            ];

            if ($data) {
                $options['json'] = $data;
            }

            $response = $client->request($method, "{$this->baseUrl}{$endpoint}", $options);
            
            if ($response->getHeader('Content-Type')[0] === 'application/pdf') {
                return $response->getBody();
            }

            return json_decode($response->getBody(), true);

        } catch (GuzzleException $e) {
            throw new Exception("Erro na requisição: " . $e->getMessage());
        }
    }

    public function emitirBoleto($dados)
    {
        return $this->request('POST', '/cobranca/v3/cobrancas', $dados);
    }

    public function consultarBoleto($nossoNumero)
    {
        return $this->request('GET', "/cobranca/v3/cobrancas/{$nossoNumero}");
    }

    public function baixarBoleto($nossoNumero, $motivoBaixa = 'ACERTOS')
    {
        return $this->request('POST', "/cobranca/v3/cobrancas/{$nossoNumero}/baixar", [
            'motivoBaixa' => $motivoBaixa
        ]);
    }

    public function recuperarPDF($nossoNumero)
    {
        return $this->request('GET', "/cobranca/v3/cobrancas/{$nossoNumero}/pdf");
    }
}
