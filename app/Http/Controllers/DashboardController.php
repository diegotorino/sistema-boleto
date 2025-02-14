<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Total de boletos
        $totalBoletos = Boleto::count();

        // Valor total dos boletos
        $valorTotal = Boleto::sum('valor_nominal');

        // Total de boletos em aberto (status = EMITIDO)
        $boletosEmAberto = Boleto::where('status', 'EMITIDO')->count();

        // Últimos 10 boletos
        $ultimosBoletos = Boleto::latest()->take(10)->get();

        return view('dashboard', compact(
            'totalBoletos',
            'valorTotal',
            'boletosEmAberto',
            'ultimosBoletos'
        ));
    }
}
