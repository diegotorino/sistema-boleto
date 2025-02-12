<?php

namespace App\Console\Commands;

use App\Models\Boleto;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AtualizarStatusBoletos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boletos:atualizar-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza o status dos boletos vencidos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando atualização de status dos boletos...');

        // Atualiza boletos vencidos
        $boletosAtualizados = Boleto::where('status', 'pendente')
            ->where('vencimento', '<', Carbon::today())
            ->update(['status' => 'vencido']);

        $this->info("Boletos atualizados: {$boletosAtualizados}");

        // TODO: Implementar consulta à API do banco para verificar boletos pagos

        return Command::SUCCESS;
    }
}
