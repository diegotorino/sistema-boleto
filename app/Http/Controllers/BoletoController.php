<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Str;

class BoletoController extends Controller
{
    public function index(Request $request)
    {
        $query = Boleto::with('cliente');

        // Filtro por cliente
        if ($request->filled('cliente')) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->cliente . '%')
                  ->orWhere('cpf_cnpj', 'like', '%' . $request->cliente . '%');
            });
        }

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por data
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
        $clientes = Cliente::orderBy('nome')->get();
        return view('boletos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'valor' => 'required|numeric|min:0',
                'vencimento' => 'required|date|after:today',
                'descricao' => 'nullable|string'
            ]);

            // Salva o boleto no banco de dados
            $boleto = Boleto::create([
                'cliente_id' => $request->cliente_id,
                'valor' => $request->valor,
                'vencimento' => $request->vencimento,
                'descricao' => $request->descricao,
                'status' => 'pendente',
                // Campos temporários até integração com o Inter
                'nosso_numero' => 'TEMP-' . Str::random(10),
                'linha_digitavel' => 'Aguardando integração com o banco',
                'codigo_barras' => 'Aguardando integração com o banco'
            ]);

            if (!$boleto) {
                throw new Exception('Não foi possível salvar o boleto.');
            }

            return redirect()
                ->route('boletos.index')
                ->with('success', 'Boleto cadastrado com sucesso! A integração com o banco será implementada em breve.');

        } catch (Exception $e) {
            Log::error('Erro ao cadastrar boleto: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Erro ao cadastrar boleto: ' . $e->getMessage());
        }
    }

    public function show(Boleto $boleto)
    {
        return view('boletos.show', compact('boleto'));
    }
}
