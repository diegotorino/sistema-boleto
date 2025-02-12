<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('boletos', function (Blueprint $table) {
            $table->id();
            $table->decimal('valor', 10, 2);
            $table->date('vencimento');
            $table->string('pagador_nome');
            $table->string('pagador_cpf_cnpj');
            $table->string('pagador_endereco');
            $table->string('pagador_cidade');
            $table->string('pagador_estado', 2);
            $table->string('pagador_cep');
            $table->string('nosso_numero')->nullable();
            $table->string('linha_digitavel')->nullable();
            $table->string('codigo_barras')->nullable();
            $table->enum('status', ['pendente', 'pago', 'vencido', 'cancelado'])->default('pendente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletos');
    }
};
