<?php

namespace App\Imports;

use App\Models\Afastamento;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AfastamentoImport implements ToModel, WithHeadingRow, WithCustomCsvSettings
{
    public function model(array $row)
    {
        return new Afastamento([
            'data_psc' => $row['data_psc'] ?? null,
            'empresa' => $row['empresa'] ?? null,
            'unidade' => $row['unidade'] ?? null,
            'cargo' => $row['cargo'] ?? null,
            'setor' => $row['setor'] ?? null,
            'nome' => $row['nome'] ?? null,
            'data_notificacao' => $row['data_notificacao_andamento_processo_shopee'] ?? $row['data_notificacao'] ?? null,
            'andamento_processo_shopee' => $row['andamento_processo_shopee'] ?? null,
            'cpf' => $row['cpf'] ?? null,
            'data_nascimento' => $row['data_de_nascimento'] ?? $row['data_nascimento'] ?? null,
            'ano_nascimento' => $row['ano_nascimento'] ?? null,
            'idade' => $row['idade'] ?? null,
            'genero' => $row['genêro'] ?? $row['genero'] ?? null,
            'codigo' => $row['código'] ?? $row['codigo'] ?? null,
            'data_admissao' => $row['data_de_admissao'] ?? $row['data_admissao'] ?? null,
            'data_carta_dut_enviada_assinatura' => $row['carta_dut_enviada_para_assinatura'] ?? $row['data_carta_dut_enviada_assinatura'] ?? null,
            'data_carta_dut_recebida_assinada' => $row['carta_dut_recebida_ja_assinada'] ?? $row['data_carta_dut_recebida_assinada'] ?? null,
            'data_carta_dut_enviada_colaborador' => $row['carta_dut_enviada_ao_colaborador'] ?? $row['data_carta_dut_enviada_colaborador'] ?? null,
            'data_ultimo_dia_trabalhado' => $row['data_do_ultimo_dia_trabalhado_dut'] ?? $row['data_ultimo_dia_trabalhado'] ?? null,
            'condicao_abertura_cat' => $row['condicao_abertura_cat'] ?? null,
            'cid' => $row['cid'] ?? null,
            'patologia' => $row['patologia'] ?? null,
            'descricao_patologia' => $row['descricao_da_patologia'] ?? $row['descricao_patologia'] ?? null,
            'especie_beneficio_inss' => $row['especie_do_beneficio_inss'] ?? $row['especie_beneficio_inss'] ?? null,
            'afastada_atividades' => $row['afastada_das_atividades'] ?? $row['afastada_atividades'] ?? null,
            'afastados_inss' => $row['afastados_inss'] ?? null,
            'limbo_previdenciario' => $row['limbo_previdenciario'] ?? null,
            'alta_antecipada' => $row['alta_antecipada'] ?? null,
            'entrada_pericia' => $row['entrada_da_pericia'] ?? $row['entrada_pericia'] ?? null,
            'data_pericia' => $row['data_da_pericia'] ?? $row['data_pericia'] ?? null,
            'tipo_pericia' => $row['tipo_de_pericia'] ?? $row['tipo_pericia'] ?? null,
            'pericia_realizada' => $row['pericia_realizada'] ?? null,
            'numero_beneficio' => $row['numero_beneficio'] ?? null,
            'status_pericia' => $row['status_da_pericia'] ?? $row['status_pericia'] ?? null,
            'motivo' => $row['motivo'] ?? null,
            'nexo_tecnico' => $row['nexo_tecnico'] ?? null,
            'contestacao' => $row['contestacao'] ?? null,
            'termino_previsto_beneficio' => $row['termino_previsto_beneficio'] ?? null,
            'notificar_shopee_retornado' => $row['notificar_shopee_sobre_o_retorno_do_colaborador_10_dias_antes_do_final_do_beneficio'] ?? $row['notificar_shopee_retornado'] ?? null,
            'data_prevista_exame' => $row['data_prevista_exame_de_retorno_ao_trabalho'] ?? $row['data_prevista_exame'] ?? null,
            'clinica' => $row['clinica'] ?? null,
            'afastamento_inicial' => $row['afastamento_inicial'] ?? null,
            'data_recebimento_aso' => $row['data_de_recebimento_do_aso'] ?? $row['data_recebimento_aso'] ?? null,
            'data_envio_aso_shopee' => $row['data_envio_aso_shopee'] ?? null,
            'status_atual' => $row['status_atual'] ?? null,
            'data_retorno_atividades' => $row['data_do_retorno_das_atividades'] ?? $row['data_retorno_atividades'] ?? null,
            'periodo_restricao' => $row['periodo_de_restricao'] ?? $row['periodo_restricao'] ?? null,
        ]);
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => "\t",
            'input_encoding' => 'UTF-8',
            'enclosure' => '"',
        ];
    }
}