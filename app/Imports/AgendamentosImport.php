<?php

namespace App\Imports;

use App\Models\Agendamento;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AgendamentosImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Agendamento([
            'colaborador_id' => $row['colaborador_id'], // ID do colaborador
            'empresa_id' => $row['empresa_id'], // ID da empresa solicitante
            'unidade_id' => $row['unidade_id'], // ID da unidade
            'estado_atendimento' => $row['estado_atendimento'], // Estado do Atendimento
            'cidade_atendimento' => $row['cidade_atendimento'], // Cidade do Atendimento
            'data_exame' => \Carbon\Carbon::parse($row['data_exame']), // Data do Exame
            'horario_exame' => $row['horario_exame'], // Horário do Exame
            'clinica_agendada' => $row['clinica_agendada'], // Clínica Agendada
            'nome_funcionario' => $row['nome_funcionario'], // Nome do Funcionário
            'contato_whatsapp' => $row['contato_whatsapp'], // Contato WhatsApp
            'doc_identificacao_rg' => $row['doc_identificacao_rg'], // RG
            'doc_identificacao_cpf' => $row['doc_identificacao_cpf'], // CPF
            'data_nascimento' => \Carbon\Carbon::parse($row['data_nascimento']), // Data de Nascimento
            'data_admissao' => \Carbon\Carbon::parse($row['data_admissao']), // Data de Admissão
            'funcao' => $row['funcao'], // Função
            'setor' => $row['setor'], // Setor
            'tipo_exame' => $row['tipo_exame'], // Tipo de Exame
            'status' => $row['status'], // Status do Exame
            'sla' => $row['sla'], // SLA
            'data_solicitacao' => \Carbon\Carbon::parse($row['data_solicitacao']), // Data e Hora da Solicitação
            'senha_confirmacao' => $row['senha_confirmacao'] ?? null, // Senha de Confirmação
            'nome_solicitante' => $row['nome_solicitante'], // Nome do Solicitante
            'origem_agendamento' => $row['origem_agendamento'], // Origem do Agendamento
            'email_solicitante' => $row['email_solicitante'] ?? null, // E-mail do Solicitante
            'data_devolutiva' => \Carbon\Carbon::parse($row['data_devolutiva']), // Data e Hora da Devolutiva
            'comparecimento' => $row['comparecimento'], // Comparecimento
            'user_id' => $row['user_id'], // ID do usuário que fez o agendamento
        ]);
    }
    
}
