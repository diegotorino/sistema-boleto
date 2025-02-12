<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Dados estáticos para o dashboard
        $data = [
            'totalBoletos' => 150,
            'valorTotal' => 15750.50,
            'boletosPendentes' => 45,
            'boletosVencidos' => 15,
            
            // Dados estáticos para o gráfico de boletos por mês
            'dadosPorMes' => [30, 42, 35, 27, 43, 38, 36, 31, 40, 45, 32, 38],
            
            // Dados estáticos para o gráfico de status
            'statusBoletos' => [90, 45, 15], // Pagos, Pendentes, Vencidos
            
            // Dados estáticos para a tabela de últimos boletos
            'ultimosBoletos' => [
                [
                    'cliente' => 'João Silva',
                    'valor' => 850.00,
                    'vencimento' => '2024-03-15',
                    'status' => 'Pendente'
                ],
                [
                    'cliente' => 'Maria Santos',
                    'valor' => 1200.00,
                    'vencimento' => '2024-03-10',
                    'status' => 'Pago'
                ],
                [
                    'cliente' => 'Pedro Oliveira',
                    'valor' => 750.00,
                    'vencimento' => '2024-02-28',
                    'status' => 'Vencido'
                ],
                [
                    'cliente' => 'Ana Costa',
                    'valor' => 980.00,
                    'vencimento' => '2024-03-20',
                    'status' => 'Pendente'
                ],
                [
                    'cliente' => 'Carlos Souza',
                    'valor' => 1500.00,
                    'vencimento' => '2024-03-05',
                    'status' => 'Pago'
                ]
            ]
        ];

        return view('dashboard', $data);
    }
}
