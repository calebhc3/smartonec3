<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->string('comparecimento')->nullable(); // Campo "comparecimento" pode ser nulo
        });
    }
    
    public function down()
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropColumn('comparecimento'); // Remove o campo se a migração for revertida
        });
    }
};
