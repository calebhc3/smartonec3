<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('afastamentos', function (Blueprint $table) {
            $table->string('cnpj_unidade') // CNPJ com máscara (14 dígitos + formatação)
                ->nullable()
                ->after('nome_unidade'); // Posiciona após o campo nome_unidade
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('afastamentos', function (Blueprint $table) {
            $table->dropColumn('cnpj_unidade');
        });
    }
};