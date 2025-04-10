<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->string('cnpj_unidade', 18)->nullable()->after('id'); // CNPJ com máscara
            $table->string('nome_unidade')->nullable()->after('cnpj_unidade');
        });
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropColumn(['cnpj_unidade', 'nome_unidade']);
        });
    }
};
