<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('afastamentos', function (Blueprint $table) {
            // Primeiro remove a coluna antiga
            $table->dropColumn('afastamento_inicial');
        });

        Schema::table('afastamentos', function (Blueprint $table) {
            // Depois adiciona de novo com o novo tipo
            $table->string('afastamento_inicial')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('afastamentos', function (Blueprint $table) {
            // Reverte para tipo `date`
            $table->dropColumn('afastamento_inicial');
        });

        Schema::table('afastamentos', function (Blueprint $table) {
            $table->date('afastamento_inicial')->nullable();
        });
    }
};
