<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('afastamentos', function (Blueprint $table) {
            $table->dropColumn('unidade');
            $table->dropColumn('andamento_processo_shopee');
        });
    }

    public function down(): void
    {
        Schema::table('afastamentos', function (Blueprint $table) {
            $table->string('unidade')->nullable(); // ou `->required()` se quiser obrigatório
            $table->string('andamento_processo_shopee')->nullable(); // ou `->required()` se quiser obrigatório
        });
    }
};
