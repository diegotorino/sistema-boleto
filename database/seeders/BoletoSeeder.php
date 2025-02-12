<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BoletoSeeder extends Seeder
{
    public function run()
    {
        $clientes = ['João Silva', 'Maria Santos', 'Pedro Oliveira', 'Ana Costa', 'Carlos Souza'];
        $hoje = Carbon::now();

        // Criar boletos dos últimos 6 meses
        for ($i = 0; $i < 50; $i++) {
            $status = ['Pendente', 'Pago', 'Vencido'][rand(0, 2)];
            $dataCreated = Carbon::now()->subMonths(rand(0, 5))->subDays(rand(0, 30));
            
            DB::table('boletos')->insert([
                'cliente' => $clientes[array_rand($clientes)],
                'valor' => rand(100, 1000) + (rand(0, 99) / 100),
                'vencimento' => $dataCreated->copy()->addDays(30),
                'status' => $status,
                'created_at' => $dataCreated,
                'updated_at' => $dataCreated,
            ]);
        }
    }
}
