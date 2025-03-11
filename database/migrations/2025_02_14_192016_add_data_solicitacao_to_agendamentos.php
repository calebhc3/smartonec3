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
            $table->timestamp('data_solicitacao')->nullable()->after('user_id');
        });
    }
    
    public function down()
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropColumn('data_solicitacao');
        });
    }
    
};
