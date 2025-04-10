<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // Ex: 'agendamento', 'ausência', 'empresa'
            $table->text('mensagem'); // Detalhe da ação registrada
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Usuário que fez a ação
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
