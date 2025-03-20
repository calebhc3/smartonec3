<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendamentosTable extends Migration
{
    public function up()
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('cidade_atendimento');
            $table->string('estado_atendimento');
            $table->date('data_exame');
            $table->time('horario_exame');
            $table->string('nome_funcionario');
            $table->string('contato_whatsapp');
            $table->string('doc_identificacao_rg');
            $table->string('doc_identificacao_cpf');
            $table->date('data_nascimento');
            $table->date('data_admissao');
            $table->string('funcao');
            $table->string('setor');
            $table->string('tipo_exame'); // Pode ser um enum ou string
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agendamentos');
    }
}