<?php

namespace App\Imports;

use App\Models\Agendamento;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AgendamentosImport implements ToModel, WithHeadingRow
{
    private $empresaSelecionada;
    private $defaultStatus = 'Cancelado';

    public function __construct($empresaSelecionada)
    {
        $this->empresaSelecionada = $empresaSelecionada;
    }

    public function model(array $row)
    {
        try {
            $processedRow = $this->preProcessRow($row);
            
            // Validação com mensagens personalizadas
            $validator = Validator::make($processedRow, [
                'empresa_id' => 'required|exists:empresas,id',
                'estado_atendimento' => 'required|in:' . implode(',', array_keys(self::getEstadosBrasileiros())),
                'cidade_atendimento' => 'required|string|max:255',
                'data_exame' => ['required', function ($attribute, $value, $fail) {
                    if (!$this->isValidDate($value)) {
                        $fail('O campo data exame deve estar no formato dd/mm/YYYY');
                    }
                }],
                'horario_exame' => 'required|date_format:H:i:s',
                'nome_funcionario' => 'required|string|max:255',
                'doc_identificacao_cpf' => 'required|string|max:14',
                'tipo_exame' => 'required|in:' . implode(',', array_keys(self::getTiposExame())),
            ], [
                'required' => 'O campo :attribute é obrigatório',
                'date_format' => 'O campo :attribute deve estar no formato HH:MM:SS',
                'in' => 'O valor do campo :attribute é inválido',
                'max' => 'O campo :attribute não pode ter mais que :max caracteres'
            ]);
    
            if ($validator->fails()) {
                Log::warning('Erro de validação: ', $validator->errors()->all());
                Log::debug('Dados processados com erro: ', $processedRow);
                return null;
            }
    
            // Processa as datas
            $dataExame = $this->parseDate($processedRow['data_exame']);
            $dataNascimento = $this->parseDate($processedRow['data_nascimento'] ?? null);
            $dataAdmissao = $this->parseDate($processedRow['data_admissao'] ?? null);
            $dataSolicitacao = $this->parseDate($processedRow['data_solicitacao'] ?? null);
            $dataDevolutiva = $this->parseDate($processedRow['data_devolutiva'] ?? null);
    
            // Cria o agendamento
            return new Agendamento([
                'empresa_id' => $this->empresaSelecionada,
                'cnpj_unidade' => $processedRow['cnpj_unidade'] ?? null,
                'nome_unidade' => $this->getNomeUnidadePorCnpj($processedRow['cnpj_unidade'] ?? ''),
                'estado_atendimento' => $processedRow['estado_atendimento'],
                'cidade_atendimento' => $processedRow['cidade_atendimento'],
                'data_exame' => $dataExame,
                'horario_exame' => $processedRow['horario_exame'],
                'clinica_agendada' => $processedRow['clinica_agendada'] ?? null,
                'nome_funcionario' => $processedRow['nome_funcionario'],
                'contato_whatsapp' => $processedRow['contato_whatsapp'] ?? null,
                'doc_identificacao_rg' => $processedRow['doc_identificacao_rg'] ?? null,
                'doc_identificacao_cpf' => $processedRow['doc_identificacao_cpf'],
                'data_nascimento' => $dataNascimento,
                'data_admissao' => $dataAdmissao,
                'funcao' => $processedRow['funcao'] ?? null,
                'setor' => $processedRow['setor'] ?? null,
                'tipo_exame' => $processedRow['tipo_exame'],
                'status' => $processedRow['status'] ?? $this->defaultStatus,
                'sla' => $processedRow['sla'] ?? null,
                'data_solicitacao' => $dataSolicitacao,
                'nome_solicitante' => $processedRow['nome_solicitante'] ?? null,
                'email_solicitante' => $processedRow['email_solicitante'] ?? null,
                'whatsapp_solicitante' => $processedRow['whatsapp_solicitante'] ?? null,
                'data_devolutiva' => $dataDevolutiva,
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao importar agendamento: ' . $e->getMessage());
            Log::debug('Linha com erro: ', $row);
            return null;
        }
    }

    private function isValidDate($value): bool
{
    if (empty($value)) {
        return false;
    }

    // Verifica formato dd/mm/YYYY
    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
        $parts = explode('/', $value);
        return checkdate($parts[1], $parts[0], $parts[2]);
    }

    // Verifica se é numérico (formato Excel)
    if (is_numeric($value)) {
        try {
            Date::excelToDateTimeObject($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Tenta parse padrão
    try {
        Carbon::parse($value);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}
    private function preProcessRow(array $row): array
    {
        $headerMap = [
            // Mapeamento direto para campos com underscore
            'estado_de_atendimento' => 'estado_atendimento',
            'cidade_de_atendimento' => 'cidade_atendimento',
            'data_do_exame' => 'data_exame',
            'horario_do_exame' => 'horario_exame',
            'nome_do_funcionario' => 'nome_funcionario',
            'tipo_de_exame' => 'tipo_exame',
            
            // Mapeamento para campos sem underscore
            'estadodeatendimento' => 'estado_atendimento',
            'cidadedeatendimento' => 'cidade_atendimento',
            'datadoexame' => 'data_exame',
            'horariodoexame' => 'horario_exame',
            'nomedofuncionario' => 'nome_funcionario',
            'tipodeexame' => 'tipo_exame',
            
            // Outros campos importantes
            'cpf' => 'doc_identificacao_cpf',
            'rg' => 'doc_identificacao_rg',
            'data_de_nascimento' => 'data_nascimento',
            'datadenascimento' => 'data_nascimento',
            'data_de_admissao' => 'data_admissao',
            'datadeadmissao' => 'data_admissao',
            'data_da_solicitacao' => 'data_solicitacao',
            'datadasolicitacao' => 'data_solicitacao',
            'data_da_devolutiva' => 'data_devolutiva',
            'datadadevolutiva' => 'data_devolutiva',
            'contato_whatsapp' => 'contato_whatsapp',
            'contatowhatsapp' => 'contato_whatsapp',
            'clinica_agendada' => 'clinica_agendada',
            'clinicaagendada' => 'clinica_agendada'
        ];
    
        $processed = [];
        foreach ($row as $key => $value) {
            // Normaliza a chave removendo caracteres especiais e espaços
            $cleanKey = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $key));
            
            // Aplica o mapeamento ou usa a chave original
            $mappedKey = $headerMap[$cleanKey] ?? $cleanKey;
            
            // Limpa os valores (remove caracteres não numéricos de CPF, CNPJ e RG)
            if (is_string($value)) {
                $value = trim($value);
                if (in_array($mappedKey, ['doc_identificacao_cpf', 'doc_identificacao_rg', 'cnpj_unidade'])) {
                    $value = preg_replace('/[^0-9]/', '', $value);
                }
            }
            
            $processed[$mappedKey] = $value;
        }
    
        // Adiciona campos obrigatórios que não vem do arquivo
        $processed['empresa_id'] = $this->empresaSelecionada;
        
        // Log para depuração
        Log::debug('Dados após processamento:', $processed);
        
        return $processed;
    }

    private function parseDate($value)
    {
        if (empty($value) || $value === 'N/A') {
            return null;
        }
    
        try {
            // Se for numérico (formato Excel)
            if (is_numeric($value)) {
                return Carbon::instance(Date::excelToDateTimeObject($value));
            }
    
            // Tentar parsear como data em formato brasileiro (dd/mm/YYYY)
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
            }
    
            // Tentar parsear data e hora (dd/mm/YYYY H:i)
            if (preg_match('/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y H:i', $value);
            }
    
            // Tentar outros formatos comuns
            return Carbon::parse($value);
        } catch (\Exception $e) {
            Log::warning("Formato de data inválido: {$value} - Erro: " . $e->getMessage());
            return null;
        }
    }
        private function getNomeUnidadePorCnpj(string $cnpjUnidade): string
        {
            $cnpjUnidade = preg_replace('/\D/', '', $cnpjUnidade);
            if (!preg_match('/^\d{14}$/', $cnpjUnidade)) {
                Log::warning("CNPJ inválido: {$cnpjUnidade}");
                return 'CNPJ inválido';
            }

            $token = env('RECEITA_WS_TOKEN');
            $url = "https://www.receitaws.com.br/v1/cnpj/{$cnpjUnidade}?token={$token}";
            $client = new Client(['timeout' => 5, 'verify' => false]);

            try {
                $response = $client->get($url);
                $data = json_decode($response->getBody()->getContents(), true);
                return $data['nome'] ?? 'Nome não encontrado';
            } catch (RequestException $e) {
                Log::error("Erro API ({$cnpjUnidade}): " . $e->getMessage());
                return 'Erro API';
            } catch (\Exception $e) {
                Log::error("Erro inesperado ({$cnpjUnidade}): " . $e->getMessage());
                return 'Erro inesperado';
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

