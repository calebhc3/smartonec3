<?php

namespace App\Imports;

use App\Models\Afastamento;
use Maatwebsite\Excel\Concerns\ToModel;

class AfastamentoImport implements ToModel
{
    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Afastamento([
            'data_psc' => $row['data_psc'],
            'empresa' => $row['empresa'],
            'unidade' => $row['unidade'],
            'cargo' => $row['cargo'],
            'setor' => $row['setor'],
            'nome' => $row['nome'],
            'data_notificacao' => $row['data_notificacao'],
            'andamento_processo_shopee' => $row['andamento_processo_shopee'],
            'cpf' => $row['cpf'],
            'data_nascimento' => $row['data_nascimento'],
            'idade' => $row['idade'],
            'genero' => $row['genero'],
            'codigo' => $row['codigo'],
            'data_admissao' => $row['data_admissao'],
            'data_carta_dut_enviada_assinatura' => $row['data_carta_dut_enviada_assinatura'],
            'data_carta_dut_recebida_assinada' => $row['data_carta_dut_recebida_assinada'],
            'data_carta_dut_enviada_colaborador' => $row['data_carta_dut_enviada_colaborador'],
            'data_ultimo_dia_trabalhado' => $row['data_ultimo_dia_trabalhado'],
            'condicao_abertura_cat' => $row['condicao_abertura_cat'],
            'cid' => $row['cid'],
            'patologia' => $row['patologia'],
            'descricao_patologia' => $row['descricao_patologia'],
            'especie_beneficio_inss' => $row['especie_beneficio_inss'],
            'afastada_atividades' => $row['afastada_atividades'],
            'afastados_inss' => $row['afastados_inss'],
            'limbo_previdenciario' => $row['limbo_previdenciario'],
            'alta_antecipada' => $row['alta_antecipada'],
            'entrada_pericia' => $row['entrada_pericia'],
            'data_pericia' => $row['data_pericia'],
            'tipo_pericia' => $row['tipo_pericia'],
            'pericia_realizada' => $row['pericia_realizada'],
            'numero_beneficio' => $row['numero_beneficio'],
            'status_pericia' => $row['status_pericia'],
            'motivo' => $row['motivo'],
            'nexo_tecnico' => $row['nexo_tecnico'],
            'contestacao' => $row['contestacao'],
            'termino_previsto_beneficio' => $row['termino_previsto_beneficio'],
            'notificar_shopee_retornado' => $row['notificar_shopee_retornado'],
            'data_prevista_exame' => $row['data_prevista_exame'],
            'clinica' => $row['clinica'],
            'afastamento_inicial' => $row['afastamento_inicial'],
            'data_recebimento_aso' => $row['data_recebimento_aso'],
            'data_envio_aso_shopee' => $row['data_envio_aso_shopee'],
            'status_atual' => $row['status_atual'],
            'data_retorno_atividades' => $row['data_retorno_atividades'],
            'periodo_restricao' => $row['periodo_restricao'],
        ]);
    }
}
