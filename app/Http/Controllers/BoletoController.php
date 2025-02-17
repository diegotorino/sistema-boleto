<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\Cliente;
use App\Services\InterBoletoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BoletoController extends Controller
{
    protected $boletoService;

    public function __construct(InterBoletoService $boletoService)
    {
        $this->boletoService = $boletoService;

        // Garante que o diretório de boletos existe
        if (!Storage::disk('public')->exists('boletos')) {
            Storage::disk('public')->makeDirectory('boletos');
        }
    }

    public function index()
    {
        $boletos = Boleto::latest()->paginate(10);
        return view('boletos.index', compact('boletos'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        return view('boletos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'seuNumero' => 'required|string|max:255',
            'valorNominal' => 'required|numeric|min:0.01',
            'dataVencimento' => 'required|date|after:today',
            'numDiasAgenda' => 'required|integer|min:1',
            'pagador.nome' => 'required|string|max:255',
            'pagador.tipoPessoa' => 'required|in:FISICA,JURIDICA',
            'pagador.cpfCnpj' => 'required|string',
            'pagador.email' => 'nullable|email',
            'pagador.endereco.cep' => 'required|string',
            'pagador.endereco.logradouro' => 'required|string',
            'pagador.endereco.numero' => 'required|string',
            'pagador.endereco.complemento' => 'nullable|string',
            'pagador.endereco.bairro' => 'required|string',
            'pagador.endereco.cidade' => 'required|string',
            'pagador.endereco.uf' => 'required|string|size:2'
        ]);

        try {
            Log::info('Dados recebidos para criação de boleto', $validated);

            // Criar boleto no Inter
            $response = $this->boletoService->createBoleto([
                'seuNumero' => $validated['seuNumero'],
                'valorNominal' => $validated['valorNominal'],
                'dataVencimento' => $validated['dataVencimento'],
                'numDiasAgenda' => $validated['numDiasAgenda'],
                'pagador' => $validated['pagador']
            ]);

            if (!isset($response['success']) || !$response['success']) {
                Log::error('Erro ao criar boleto no Inter', ['error' => $response['message'] ?? 'Erro desconhecido']);
                return back()
                    ->withInput()
                    ->with('error', 'Erro ao gerar boleto: ' . ($response['message'] ?? 'Erro desconhecido'));
            }

            // Salvar no banco de dados
            $boleto = Boleto::create([
                'cliente_id' => $validated['cliente_id'],
                'seu_numero' => $validated['seuNumero'],
                'valor_nominal' => $validated['valorNominal'],
                'data_vencimento' => $validated['dataVencimento'],
                'num_dias_agenda' => $validated['numDiasAgenda'],
                'pagador_nome' => $validated['pagador']['nome'],
                'pagador_tipo' => $validated['pagador']['tipoPessoa'],
                'pagador_cpf_cnpj' => $validated['pagador']['cpfCnpj'],
                'pagador_email' => $validated['pagador']['email'],
                'pagador_endereco' => $validated['pagador']['endereco']['logradouro'],
                'pagador_numero' => $validated['pagador']['endereco']['numero'],
                'pagador_complemento' => $validated['pagador']['endereco']['complemento'],
                'pagador_bairro' => $validated['pagador']['endereco']['bairro'],
                'pagador_cidade' => $validated['pagador']['endereco']['cidade'],
                'pagador_uf' => $validated['pagador']['endereco']['uf'],
                'pagador_cep' => $validated['pagador']['endereco']['cep'],
                'codigo_solicitacao' => $response['data']['codigoSolicitacao'],
                'status' => 'EMITIDO',
                'pdf_path' => null
            ]);

            // Baixar e salvar o PDF
            $pdfResponse = $this->boletoService->getBoletoDetails($response['data']['codigoSolicitacao']);
            
            if (isset($pdfResponse['success']) && $pdfResponse['success']) {
                $pdfPath = 'boletos/' . $boleto->id . '.pdf';
                $boleto->update(['pdf_path' => $pdfPath]);
            } else {
                Log::warning('Não foi possível baixar o PDF do boleto', [
                    'boleto_id' => $boleto->id,
                    'error' => $pdfResponse['message'] ?? 'Erro desconhecido'
                ]);
            }

            return redirect()
                ->route('boletos.show', $boleto)
                ->with('success', 'Boleto gerado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao processar boleto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erro ao processar boleto: ' . $e->getMessage());
        }
    }

    public function show(Boleto $boleto)
    {
        return view('boletos.show', compact('boleto'));
    }

    public function pagar(Boleto $boleto)
    {
        try {
            $response = $this->boletoService->pagarBoleto($boleto->codigo_solicitacao);

            if (!$response['success']) {
                return back()->with('error', $response['message']);
            }

            $boleto->update(['status' => 'PAGO']);

            return back()->with('success', 'Pagamento simulado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao simular pagamento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'boleto_id' => $boleto->id
            ]);

            return back()->with('error', 'Erro ao simular pagamento: ' . $e->getMessage());
        }
    }

    public function cancelar(Boleto $boleto)
    {
        try {
            $response = $this->boletoService->cancelarBoleto($boleto->codigo_solicitacao);

            if (!$response['success']) {
                return back()->with('error', $response['message']);
            }

            $boleto->update(['status' => 'CANCELADO']);

            return back()->with('success', 'Boleto cancelado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao cancelar boleto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'boleto_id' => $boleto->id
            ]);

            return back()->with('error', 'Erro ao cancelar boleto: ' . $e->getMessage());
        }
    }

    public function pdf(Boleto $boleto)
    {
        if (!$boleto->pdf_path || !Storage::disk('public')->exists($boleto->pdf_path)) {
            // Se o PDF não existir, tenta baixar novamente
            $pdf = $this->boletoService->getBoletoDetails($boleto->codigo_solicitacao);
            
            if (!$pdf['success']) {
                return response()->json(['error' => 'PDF não encontrado'], 404);
            }

            $boleto->update(['pdf_path' => $pdf['data']['pdf_path']]);
        }

        return response()->file(storage_path('app/public/' . $boleto->pdf_path));
    }
}
