<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToAgendamentosTable extends Migration
{
    public function up()
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->string('status')->default('pendente'); // Valores possíveis: pendente, realizado
        });
    }

    public function down()
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}