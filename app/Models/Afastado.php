<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afastado extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_psc', 'empresa', 'unidade', 'cargo', 'setor', 'nome', 'data_notificacao', 'andamento_processo_shopee', 'cpf',
        'data_nascimento', 'idade', 'genero', 'codigo', 'data_admissao',
        'data_carta_dut_enviada_assinatura', 'data_carta_dut_recebida_assinada', 'data_carta_dut_enviada_colaborador',
        'data_ultimo_dia_trabalhado', 'condicao_abertura_cat', 'cid', 'patologia', 'descricao_patologia',
        'especie_beneficio_inss', 'afastada_atividades', 'afastados_inss', 'limbo_previdenciario',
        'alta_antecipada', 'entrada_pericia', 'data_pericia', 'tipo_pericia', 'pericia_realizada',
        'numero_beneficio', 'status_pericia', 'motivo', 'nexo_tecnico', 'contestacao',
        'termino_previsto_beneficio', 'notificar_shopee_retorno', 'data_prevista_exame_retorno', 'clinica',
        'afastamento_inicial', 'data_recebimento_aso', 'data_envio_aso_shopee',
        'status_atual', 'data_retorno_atividades', 'periodo_restricao',
    ];
}

