<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('afastamentos', function (Blueprint $table) {
            $table->string('empresa')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('afastamentos', function (Blueprint $table) {
            $table->string('empresa')->nullable(false)->change();
        });
    }
};