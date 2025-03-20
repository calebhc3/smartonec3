<?php

namespace App\Imports;

use App\Models\Agendamento;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AgendamentosImport implements ToModel, WithHeadingRow
{
    private $empresaSelecionada;

    public function __construct($empresaSelecionada)
    {
        $this->empresaSelecionada = $empresaSelecionada;
    }

    public function model(array $row)
    {
        try {
            // Validação básica (Opcional, mas recomendado)
            $validator = Validator::make($row, [
                'empresa_id' => 'required|exists:empresas,id',
                'unidade_id' => 'required|exists:unidades,id',
                'estado_atendimento' => 'required|in:' . implode(',', array_keys(self::getEstadosBrasileiros())),
                'cidade_atendimento' => 'required|string',
                'data_exame' => 'required',
                'horario_exame' => 'required',
                'nome_funcionario' => 'required|string',
                'doc_identificacao_cpf' => 'required|string',
                'tipo_exame' => 'required|in:' . implode(',', array_keys(self::getTiposExame())),
                'status' => 'nullable|in:' . implode(',', array_keys(self::getStatusExame())),
                'sla' => 'nullable|in:' . implode(',', array_keys(self::getSlaOptions())),
            ]);

            if ($validator->fails()) {
                Log::warning('Erro de validação na importação: ' . json_encode($validator->errors()->all()));
                return null;
            }
            Log::debug('Data Exame: ' . $row['data_exame']);

            // Criando um novo agendamento
            $agendamento = new Agendamento([
                'empresa_id' => $this->empresaSelecionada,
                'unidade_id' => $row['unidade_id'],
                'estado_atendimento' => $row['estado_atendimento'],
                'cidade_atendimento' => $row['cidade_atendimento'],
                'data_exame' => isset($row['data_exame']) ? \Carbon\Carbon::createFromFormat('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_exame'])->format('Y-m-d')) : null,
                'horario_exame' => $row['horario_exame'],
                'clinica_agendada' => $row['clinica_agendada'] ?? null,
                'nome_funcionario' => $row['nome_funcionario'],
                'contato_whatsapp' => $row['contato_whatsapp'] ?? null,
                'doc_identificacao_rg' => $row['doc_identificacao_rg'] ?? null,
                'doc_identificacao_cpf' => $row['doc_identificacao_cpf'],
                'data_nascimento' => isset($row['data_nascimento']) ? \Carbon\Carbon::createFromFormat('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_nascimento'])->format('Y-m-d')) : null,
                'data_admissao' => isset($row['data_admissao']) ? \Carbon\Carbon::createFromFormat('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_admissao'])->format('Y-m-d')) : null,
                'funcao' => $row['funcao'] ?? null,
                'setor' => $row['setor'] ?? null,
                'tipo_exame' => $row['tipo_exame'],
                'status' => $row['status'] ?? 'Pendente',
                'sla' => $row['sla'] ?? null,
                'data_solicitacao' => isset($row['data_solicitacao']) ? \Carbon\Carbon::createFromFormat('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_solicitacao'])->format('Y-m-d')) : null,
                'nome_solicitante' => $row['nome_solicitante'] ?? null,
                'email_solicitante' => $row['email_solicitante'] ?? null,
                'whatsapp_solicitante' => $row['whatsapp_solicitante'] ?? null,
                'data_devolutiva' => isset($row['data_devolutiva']) ? \Carbon\Carbon::createFromFormat('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_devolutiva'])->format('Y-m-d')) : null,
                'comparecimento' => $row['comparecimento'] ?? null,
                'user_id' => auth()->id(),
            ]);

            // Salva e retorna o modelo
            $agendamento->save();
            return $agendamento;
        } catch (\Exception $e) {
            Log::error('Erro ao salvar agendamento: ' . $e->getMessage());
            return null;
        }
    }

    // Função para obter estados brasileiros
    private static function getEstadosBrasileiros(): array
    {
        return [
            'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
            'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
            'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
            'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
            'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins',
        ];
    }

    // Função para obter tipos de exame
    private static function getTiposExame(): array
    {
        return [
            'admissional' => 'Admissional',
            'periodico' => 'Periódico',
            'demissional' => 'Demissional',
            'retorno_trabalho' => 'Retorno ao Trabalho',
            'mudanca_funcao' => 'Mudança de Função',
            'avaliacao_clinica' => 'Avaliação Clínica',
        ];
    }

    // Função para obter status de exame
    private static function getStatusExame(): array
    {
        return [
            'agendado' => 'Agendado',
            'cancelado' => 'Cancelado',
            'ASO ok' => 'ASO OK',
            'ASO enviado' => 'ASO Enviado',
            'não compareceu' => 'Não Compareceu',
        ];
    }

    // Função para obter SLA (Service Level Agreement)
    private static function getSlaOptions(): array
    {
        return [
            'clinico' => 'Exame Clínico (1 dia)',
            'clinico_complementar' => 'Exame Clínico + Complementar (3 dias)',
            'clinico_acidos' => 'Exame Clínico + Ácidos (5 a 10 dias)',
        ];
    }
}

