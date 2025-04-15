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
            $table->boolean('is_prorrogado')->default(false)->after('termino_previsto_beneficio');
        });
    }
    
    public function down()
    {
        Schema::table('afastamentos', function (Blueprint $table) {
            $table->dropColumn('is_prorrogado');
        });
    }    
};
