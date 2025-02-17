<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cliente::query();

        // Filtro por nome
        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        // Filtro por CPF/CNPJ
        if ($request->filled('cpf_cnpj')) {
            $query->where('cpf_cnpj', 'like', '%' . $request->cpf_cnpj . '%');
        }

        // Filtro por email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        $clientes = $query->orderBy('nome')->paginate(10);
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clientes',
            'cpf_cnpj' => 'required|string|unique:clientes',
            'telefone' => 'nullable|string|max:20',
            'cep' => 'required|string|size:9',
            'endereco' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'uf' => 'required|string|size:2'
        ]);

        try {
            $cliente = Cliente::create([
                'nome' => $validated['nome'],
                'email' => $validated['email'],
                'cpf_cnpj' => preg_replace('/[^0-9]/', '', $validated['cpf_cnpj']),
                'telefone' => $validated['telefone'],
                'cep' => preg_replace('/[^0-9]/', '', $validated['cep']),
                'endereco' => $validated['endereco'],
                'numero' => $validated['numero'],
                'complemento' => $validated['complemento'],
                'bairro' => $validated['bairro'],
                'cidade' => $validated['cidade'],
                'uf' => strtoupper($validated['uf'])
            ]);

            return redirect()
                ->route('clientes.index')
                ->with('success', 'Cliente cadastrado com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao cadastrar cliente: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $cliente->id,
            'cpf_cnpj' => 'required|string|max:18|unique:clientes,cpf_cnpj,' . $cliente->id,
            'telefone' => 'required|string|max:15',
            'endereco' => 'required|string|max:255',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }
}
