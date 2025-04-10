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
        Schema::table('afastamentos', function (Blueprint $table) {
            $table->string('cnpj_unidade')->nullable()->after('idade'); // coloca depois de algum campo que já exista
            $table->string('nome_unidade')->nullable()->after('cnpj_unidade'); // coloca depois de algum campo que já exista
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('afastamentos', function (Blueprint $table) {
            $table->dropColumn('cnpj_unidade');
            $table->dropColumn('nome_unidade');
        });
    }
};
