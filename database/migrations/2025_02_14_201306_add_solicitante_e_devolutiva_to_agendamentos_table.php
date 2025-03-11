<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->string('nome_solicitante')->nullable()->after('data_solicitacao');
            $table->dateTime('data_devolutiva')->nullable()->after('nome_solicitante');
        });
    }
    
    public function down()
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropColumn(['nome_solicitante', 'data_devolutiva']);
        });
    }
    
};
