<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('boletos', function (Blueprint $table) {
            if (!Schema::hasColumn('boletos', 'nosso_numero')) {
                $table->string('nosso_numero')->nullable()->after('status');
            }
            if (!Schema::hasColumn('boletos', 'linha_digitavel')) {
                $table->string('linha_digitavel')->nullable()->after('nosso_numero');
            }
            if (!Schema::hasColumn('boletos', 'codigo_barras')) {
                $table->string('codigo_barras')->nullable()->after('linha_digitavel');
            }
            if (!Schema::hasColumn('boletos', 'url_pdf')) {
                $table->string('url_pdf')->nullable()->after('codigo_barras');
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
