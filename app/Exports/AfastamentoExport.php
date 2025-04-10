<?php

namespace App\Exports;

use App\Models\Afastado;
use Maatwebsite\Excel\Concerns\FromCollection;

class AfastadoExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Afastado::all()->map(function ($afastado) {
            return [
                // Dados Iniciais
                'Data PSC' => $afastado->data_psc,
                'Empresa' => $afastado->empresa,
                'Unidade' => $afastado->unidade,
                'Cargo' => $afastado->cargo,
                'Setor' => $afastado->setor,
                'Nome' => $afastado->nome,
                'Data Notificação' => $afastado->data_notificacao,
                'Andamento Processo Shopee' => $afastado->andamento_processo_shopee,
                'CPF' => $afastado->cpf,
                'Data de Nascimento' => $afastado->data_nascimento,
                'Idade' => $afastado->idade,
                'Gênero' => $afastado->genero,
                'Código' => $afastado->codigo,
                'Data Admissão' => $afastado->data_admissao,

                // Controle Interno C3 Saúde
                'Data Carta DUT Enviada para Assinatura' => $afastado->data_carta_dut_enviada_assinatura,
                'Data Carta DUT Recebida já Assinada' => $afastado->data_carta_dut_recebida_assinada,
                'Data Carta DUT Enviada ao Colaborador' => $afastado->data_carta_dut_enviada_colaborador,
                'Data Último Dia Trabalhado (DUT)' => $afastado->data_ultimo_dia_trabalhado,
                'Condição Abertura Cat' => $afastado->condicao_abertura_cat,
                'CID' => $afastado->cid,
                'Patologia' => $afastado->patologia,
                'Descrição da Patologia' => $afastado->descricao_patologia,
                'Espécie do Benefício INSS' => $afastado->especie_beneficio_inss,
                'Afastada das Atividades' => $afastado->afastada_atividades,
                'Afastados INSS' => $afastado->afastados_inss,
                'Limbo Previdenciário' => $afastado->limbo_previdenciario,

                // Dados Iniciais da Perícia
                'Alta Antecipada' => $afastado->alta_antecipada,
                'Entrada da Perícia' => $afastado->entrada_pericia,
                'Data da Perícia' => $afastado->data_pericia,
                'Tipo de Perícia' => $afastado->tipo_pericia,
                'Perícia Realizada' => $afastado->pericia_realizada,
                'Número Benefício' => $afastado->numero_beneficio,
                'Status da Perícia' => $afastado->status_pericia,
                'Motivo' => $afastado->motivo,
                'Nexo Técnico' => $afastado->nexo_tecnico,
                'Contestação' => $afastado->contestacao,

                // Notificação Shopee Retorno Colaborador
                'Término Previsto Benefício' => $afastado->termino_previsto_beneficio,
                'Notificar Shopee sobre Retorno' => $afastado->notificar_shopee_retornado,
                'Data Prevista Exame de Retorno' => $afastado->data_prevista_exame,
                'Clinica' => $afastado->clinica,
                'Afastamento Inicial' => $afastado->afastamento_inicial,
                'Data de Recebimento do ASO' => $afastado->data_recebimento_aso,
                'Data Envio ASO Shopee' => $afastado->data_envio_aso_shopee,

                // Informação para Folha de Pagamento
                'Status Atual' => $afastado->status_atual,
                'Data Retorno das Atividades' => $afastado->data_retorno_atividades,
                'Período de Restrição' => $afastado->periodo_restricao,
            ];
        });
    }
}
