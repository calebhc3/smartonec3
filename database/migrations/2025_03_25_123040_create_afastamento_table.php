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
        Schema::create('afastamentos', function (Blueprint $table) {
            $table->id();
            
            // Dados Iniciais
            $table->date('data_psc');
            $table->string('empresa');
            $table->string('cargo');
            $table->string('setor');
            $table->string('nome');
            $table->date('data_notificacao')->nullable();
            $table->string('andamento_processo_shopee')->nullable();
            $table->string('cpf')->unique();
            $table->date('data_nascimento');
            $table->integer('idade')->nullable();
            $table->string('genero');
            $table->string('codigo')->nullable();
            $table->date('data_admissao')->nullable();
    
            // Controle Interno C3 Saúde
            $table->date('data_carta_dut_enviada_assinatura')->nullable();
            $table->date('data_carta_dut_recebida_assinada')->nullable();
            $table->date('data_carta_dut_enviada_colaborador')->nullable();
            $table->date('data_ultimo_dia_trabalhado')->nullable();
            $table->boolean('condicao_abertura_cat')->default(false)->nullable();
            $table->string('cid')->nullable();
            $table->string('patologia')->nullable();
            $table->text('descricao_patologia')->nullable();
            $table->string('especie_beneficio_inss')->nullable();
            $table->boolean('afastada_atividades')->default(false)->nullable();
            $table->boolean('afastados_inss')->default(false)->nullable();
            $table->boolean('limbo_previdenciario')->default(false)->nullable();
    
            // Dados Iniciais da Perícia
            $table->boolean('alta_antecipada')->default(false)->nullable(); 
            $table->date('entrada_pericia')->nullable();
            $table->date('data_pericia')->nullable();
            $table->string('tipo_pericia')->nullable();
            $table->boolean('pericia_realizada')->default(false)->nullable();
            $table->string('numero_beneficio')->nullable();
            $table->string('status_pericia')->nullable();
            $table->text('motivo')->nullable();
            $table->boolean('nexo_tecnico')->default(false)->nullable();
            $table->boolean('contestacao')->default(false)->nullable();
    
            // Notificação Shopee Retorno Colaborador
            $table->date('termino_previsto_beneficio')->nullable();
            $table->date('notificar_shopee_retorno')->nullable();
            $table->date('data_prevista_exame_retorno')->nullable();
            $table->string('clinica')->nullable();
            $table->date('afastamento_inicial')->nullable();
            $table->date('data_recebimento_aso')->nullable();
            $table->date('data_envio_aso_shopee')->nullable();
    
            // Informação para Folha de Pagamento
            $table->string('status_atual')->nullable();
            $table->date('data_retorno_atividades')->nullable();
            $table->string('periodo_restricao')->nullable();
    
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afastamentos');
    }
};
