<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Services\InterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class BoletoController extends Controller
{
    private $interService;

    public function __construct(InterService $interService)
    {
        $this->interService = $interService;
    }

    public function index(Request $request)
    {
        $query = Boleto::query();

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_inicio')) {
            $query->where('vencimento', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->where('vencimento', '<=', $request->data_fim);
        }

        $boletos = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('boletos.index', compact('boletos'));
    }

    public function create()
    {
        return view('boletos.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'valor' => 'required|numeric|min:0.01',
                'vencimento' => 'required|date|after:today',
                'pagador_nome' => 'required|string|max:255',
                'pagador_cpf_cnpj' => 'required|string|max:18',
                'pagador_endereco' => 'required|string|max:255',
                'pagador_cidade' => 'required|string|max:255',
                'pagador_estado' => 'required|string|size:2',
                'pagador_cep' => 'required|string|max:9',
            ]);

            // Formata os dados para o Banco Inter
            $dadosInter = [
                'seuNumero' => uniqid(),
                'valorNominal' => floatval($request->valor),
                'dataVencimento' => $request->vencimento,
                'numDiasAgenda' => 60,
                'pagador' => [
                    'cpfCnpj' => preg_replace('/[^0-9]/', '', $request->pagador_cpf_cnpj),
                    'tipoPessoa' => strlen(preg_replace('/[^0-9]/', '', $request->pagador_cpf_cnpj)) > 11 ? 'JURIDICA' : 'FISICA',
                    'nome' => $request->pagador_nome,
                    'endereco' => $request->pagador_endereco,
                    'cidade' => $request->pagador_cidade,
                    'uf' => $request->pagador_estado,
                    'cep' => preg_replace('/[^0-9]/', '', $request->pagador_cep),
                ],
                'mensagem' => [
                    'linha1' => 'Pagamento referente à fatura',
                    'linha2' => 'Vencimento em ' . date('d/m/Y', strtotime($request->vencimento)),
                    'linha3' => '',
                    'linha4' => '',
                    'linha5' => ''
                ]
            ];

            // Cria o boleto no Banco Inter
            $response = $this->interService->createBoleto($dadosInter);

            // Salva o boleto no banco de dados
            $boleto = new Boleto();
            $boleto->valor = $request->valor;
            $boleto->vencimento = $request->vencimento;
            $boleto->pagador_nome = $request->pagador_nome;
            $boleto->pagador_cpf_cnpj = $request->pagador_cpf_cnpj;
            $boleto->pagador_endereco = $request->pagador_endereco;
            $boleto->pagador_cidade = $request->pagador_cidade;
            $boleto->pagador_estado = $request->pagador_estado;
            $boleto->pagador_cep = $request->pagador_cep;
            $boleto->nosso_numero = $response['nossoNumero'];
            $boleto->linha_digitavel = $response['linhaDigitavel'];
            $boleto->codigo_barras = $response['codigoBarras'];
            $boleto->status = 'pendente';
            $boleto->save();

            return redirect()
                ->route('boletos.show', $boleto)
                ->with('success', 'Boleto gerado com sucesso!');

        } catch (Exception $e) {
            Log::error('Erro ao gerar boleto: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Erro ao gerar boleto. Por favor, tente novamente.');
        }
    }

    public function show(Boleto $boleto)
    {
        try {
            // Consulta o status atualizado no Banco Inter
            $dadosInter = $this->interService->getBoleto($boleto->nosso_numero);
            
            // Atualiza o status do boleto
            if ($dadosInter['situacao'] !== $boleto->status) {
                $boleto->status = strtolower($dadosInter['situacao']);
                $boleto->save();
            }

            return view('boletos.show', compact('boleto'));

        } catch (Exception $e) {
            Log::error('Erro ao consultar boleto: ' . $e->getMessage());
            return view('boletos.show', compact('boleto'))
                ->with('error', 'Não foi possível atualizar o status do boleto.');
        }
    }

    public function pdf(Boleto $boleto)
    {
        try {
            $pdf = $this->interService->getBoletoPdf($boleto->nosso_numero);
            
            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="boleto.pdf"');

        } catch (Exception $e) {
            Log::error('Erro ao gerar PDF do boleto: ' . $e->getMessage());
            return back()->with('error', 'Erro ao gerar PDF do boleto.');
        }
    }

    public function cancel(Boleto $boleto)
    {
        try {
            $this->interService->cancelBoleto($boleto->nosso_numero);
            
            $boleto->status = 'cancelado';
            $boleto->save();

            return back()->with('success', 'Boleto cancelado com sucesso!');

        } catch (Exception $e) {
            Log::error('Erro ao cancelar boleto: ' . $e->getMessage());
            return back()->with('error', 'Erro ao cancelar boleto.');
        }
    }
}
