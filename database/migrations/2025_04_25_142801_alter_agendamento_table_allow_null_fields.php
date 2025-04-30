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
            $table->string('contato_whatsapp')->nullable()->change();
            $table->date('data_admissao')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->string('contato_whatsapp')->nullable(false)->change();
            $table->date('data_admissao')->nullable(false)->change();
        });
    }
};