<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->string('doc_identificacao_rg')->nullable()->change();
            $table->string('doc_identificacao_cpf')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->string('doc_identificacao_rg')->nullable(false)->change(); // mesma observação: se já tiver nulo, vai dar ruim
            $table->string('doc_identificacao_cpf')->nullable(false)->change();
        });
    }
};
