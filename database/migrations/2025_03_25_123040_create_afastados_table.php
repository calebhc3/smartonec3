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
        Schema::create('afastados', function (Blueprint $table) {
            $table->id();
            
            // Dados Iniciais
            $table->date('data_psc');
            $table->string('empresa');
            $table->string('unidade');
            $table->string('cargo');
            $table->string('setor');
            $table->string('nome');
            $table->date('data_notificacao');
            $table->string('andamento_processo_shopee');
            $table->string('cpf')->unique();
            $table->date('data_nascimento');
            $table->integer('idade');
            $table->string('genero');
            $table->string('codigo');
            $table->date('data_admissao');
    
            // Controle Interno C3 Saúde
            $table->date('data_carta_dut_enviada_assinatura')->nullable();
            $table->date('data_carta_dut_recebida_assinada')->nullable();
            $table->date('data_carta_dut_enviada_colaborador')->nullable();
            $table->date('data_ultimo_dia_trabalhado')->nullable();
            $table->boolean('condicao_abertura_cat')->default(false);
            $table->string('cid')->nullable();
            $table->string('patologia')->nullable();
            $table->text('descricao_patologia')->nullable();
            $table->string('especie_beneficio_inss')->nullable();
            $table->boolean('afastada_atividades')->default(false);
            $table->boolean('afastados_inss')->default(false);
            $table->boolean('limbo_previdenciario')->default(false);
    
            // Dados Iniciais da Perícia
            $table->boolean('alta_antecipada')->default(false);
            $table->date('entrada_pericia')->nullable();
            $table->date('data_pericia')->nullable();
            $table->string('tipo_pericia')->nullable();
            $table->boolean('pericia_realizada')->default(false);
            $table->string('numero_beneficio')->nullable();
            $table->string('status_pericia')->nullable();
            $table->text('motivo')->nullable();
            $table->boolean('nexo_tecnico')->default(false);
            $table->boolean('contestacao')->default(false);
    
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
        Schema::dropIfExists('afastados');
    }
};
