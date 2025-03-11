<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->string('email_solicitante')->nullable()->after('nome_solicitante');
            $table->string('whatsapp_solicitante')->nullable()->after('email_solicitante');
        });
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropColumn(['email_solicitante', 'whatsapp_solicitante']);
        });
    }
};
