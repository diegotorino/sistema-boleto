<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boletos', function (Blueprint $table) {
            $table->id();
            $table->string('seu_numero');
            $table->decimal('valor_nominal', 10, 2);
            $table->date('data_vencimento');
            $table->integer('num_dias_agenda');
            
            // Campos do pagador
            $table->string('pagador_nome');
            $table->string('pagador_tipo');
            $table->string('pagador_cpf_cnpj');
            $table->string('pagador_email')->nullable();
            $table->string('pagador_endereco');
            $table->string('pagador_numero');
            $table->string('pagador_complemento')->nullable();
            $table->string('pagador_bairro');
            $table->string('pagador_cidade');
            $table->string('pagador_uf', 2);
            $table->string('pagador_cep');
            
            // Campos do boleto
            $table->string('codigo_solicitacao');
            $table->string('status')->default('EMITIDO');
            $table->string('pdf_path')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boletos');
    }
};
