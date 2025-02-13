<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('boletos', function (Blueprint $table) {
            // Adiciona os campos necessários para o Banco Inter se eles não existirem
            if (!Schema::hasColumn('boletos', 'nosso_numero')) {
                $table->string('nosso_numero')->nullable();
            }
            if (!Schema::hasColumn('boletos', 'linha_digitavel')) {
                $table->string('linha_digitavel')->nullable();
            }
            if (!Schema::hasColumn('boletos', 'codigo_barras')) {
                $table->string('codigo_barras')->nullable();
            }
            if (!Schema::hasColumn('boletos', 'url_pdf')) {
                $table->string('url_pdf')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('boletos', function (Blueprint $table) {
            $table->dropColumn(['nosso_numero', 'linha_digitavel', 'codigo_barras', 'url_pdf']);
        });
    }
};
