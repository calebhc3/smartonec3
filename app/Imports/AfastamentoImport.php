<?php

namespace App\Imports;

use App\Models\Afastamento;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class AfastamentoImport implements ToModel, WithHeadingRow, WithCustomCsvSettings, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    // Contador de linhas para melhor mensagem de erro
    private $rowNumber = 0;

    public function model(array $row)
    {
        $this->rowNumber++;
        
        // Função melhorada para converter valores de data
        $parseDate = function ($value) {
            if (empty($value) || $value === '?' || $value === '-' || $value === 'NULL') {
                return null;
            }
            
            // Verifica se é um número (formato Excel)
            if (is_numeric($value)) {
                try {
                    $date = ExcelDate::excelToDateTimeObject($value);
                    // Validação adicional para ano com mais de 4 dígitos
                    if ($date->format('Y') > 9999) {
                        return null;
                    }
                    return $date->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            }
            
            // Remove caracteres não numéricos no início (como '=' que pode vir de fórmulas)
            $value = preg_replace('/^[^0-9]+/', '', $value);
            
            // Corrige anos com 5 dígitos
            if (preg_match('/^(\d{5})-(\d{2})-(\d{2})$/', $value, $matches)) {
                $value = substr($matches[1], -4) . '-' . $matches[2] . '-' . $matches[3];
            }
            
            // Tenta vários formatos de data
            $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y', 'd.m.Y', 'Y.m.d'];
            foreach ($formats as $format) {
                try {
                    $date = Carbon::createFromFormat($format, $value);
                    // Valida o ano
                    if ($date->year > 9999) {
                        continue;
                    }
                    return $date->format('Y-m-d');
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            return null;
        };

        // Função para converter valores SIM/NÃO para booleano
        $parseBoolean = function ($value) {
            if (is_null($value) || $value === '') {
                return null;
            }
            
            $value = mb_strtoupper(trim($value));
            if ($value === 'SIM' || $value === 'S' || $value === 'YES' || $value === 'Y' || $value === '1' || $value === 'VERDADEIRO' || $value === 'TRUE') {
                return 1;
            }
            if ($value === 'NÃO' || $value === 'NAO' || $value === 'N' || $value === 'NO' || $value === '0' || $value === 'FALSO' || $value === 'FALSE') {
                return 0;
            }
            return null;
        };

        // Função para limpar valores
        $cleanValue = function ($value) {
            if (is_null($value) || $value === '?' || $value === '-' || $value === 'NULL') {
                return null;
            }
            
            if (is_string($value)) {
                $value = trim($value);
                if (strpos($value, '=') === 0) {
                    return null;
                }
                if ($value === '') {
                    return null;
                }
            }
            
            return $value;
        };

        // Validação do campo obrigatório data_psc
        $dataPsc = $parseDate($row['data_psc'] ?? null);
        if (empty($dataPsc)) {
            throw new \Exception("Linha {$this->rowNumber}: O campo 'data_psc' é obrigatório e não pode ser vazio ou inválido");
        }

        return new Afastamento([
            'data_psc' => $dataPsc,
            'empresa' => $cleanValue($row['empresa'] ?? null),
            'nome_unidade' => $cleanValue($row['unidade'] ?? null),
            'cargo' => $cleanValue($row['cargo'] ?? null),
            'setor' => $cleanValue($row['setor'] ?? null),
            'nome' => $cleanValue($row['nome'] ?? null),
            'data_notificacao' => $parseDate($row['data_notificacao_andamento_processo_shopee'] ?? $row['data_notificacao'] ?? null),
            'andamento_processo_shopee' => $cleanValue($row['andamento_processo_shopee'] ?? null),
            'cpf' => $cleanValue($row['cpf'] ?? null),
            'data_nascimento' => $parseDate($row['data_de_nascimento'] ?? $row['data_nascimento'] ?? null),
            'ano_nascimento' => $cleanValue($row['ano_nascimento'] ?? null),
            'idade' => $cleanValue($row['idade'] ?? null),
            'genero' => $cleanValue($row['genêro'] ?? $row['genero'] ?? null),
            'codigo' => $cleanValue($row['código'] ?? $row['codigo'] ?? null),
            'data_admissao' => $parseDate($row['data_de_admissao'] ?? $row['data_admissao'] ?? null),
            'data_carta_dut_enviada_assinatura' => $parseDate($row['carta_dut_enviada_para_assinatura'] ?? $row['data_carta_dut_enviada_assinatura'] ?? null),
            'data_carta_dut_recebida_assinada' => $parseDate($row['carta_dut_recebida_ja_assinada'] ?? $row['data_carta_dut_recebida_assinada'] ?? null),
            'data_carta_dut_enviada_colaborador' => $parseDate($row['carta_dut_enviada_ao_colaborador'] ?? $row['data_carta_dut_enviada_colaborador'] ?? null),
            'data_ultimo_dia_trabalhado' => $parseDate($row['data_do_ultimo_dia_trabalhado_dut'] ?? $row['data_ultimo_dia_trabalhado'] ?? null),
            'condicao_abertura_cat' => $parseBoolean($row['condicao_abertura_cat'] ?? null),
            'cid' => $cleanValue($row['cid'] ?? null),
            'patologia' => $cleanValue($row['patologia'] ?? null),
            'descricao_patologia' => $cleanValue($row['descricao_da_patologia'] ?? $row['descricao_patologia'] ?? null),
            'especie_beneficio_inss' => $cleanValue($row['especie_do_beneficio_inss'] ?? $row['especie_beneficio_inss'] ?? null),
            'afastada_atividades' => $parseBoolean($row['afastada_das_atividades'] ?? $row['afastada_atividades'] ?? null),
            'afastados_inss' => $parseBoolean($row['afastados_inss'] ?? null),
            'limbo_previdenciario' => $parseBoolean($row['limbo_previdenciario'] ?? null),
            'alta_antecipada' => $parseBoolean($row['alta_antecipada'] ?? null),
            'entrada_pericia' => $parseDate($row['entrada_da_pericia'] ?? $row['entrada_pericia'] ?? null),
            'data_pericia' => $parseDate($row['data_da_pericia'] ?? $row['data_pericia'] ?? null),
            'tipo_pericia' => $cleanValue($row['tipo_de_pericia'] ?? $row['tipo_pericia'] ?? null),
            'pericia_realizada' => $parseBoolean($row['pericia_realizada'] ?? null),
            'numero_beneficio' => $cleanValue($row['numero_beneficio'] ?? null),
            'status_pericia' => $cleanValue($row['status_da_pericia'] ?? $row['status_pericia'] ?? null),
            'motivo' => $cleanValue($row['motivo'] ?? null),
            'nexo_tecnico' => $parseBoolean($row['nexo_tecnico'] ?? null),
            'contestacao' => $parseBoolean($row['contestacao'] ?? null),
            'termino_previsto_beneficio' => $parseDate($row['termino_previsto_beneficio'] ?? null),
            'notificar_shopee_retorno' => $parseDate($row['notificar_shopee_sobre_o_retorno_do_colaborador_10_dias_antes_do_final_do_beneficio'] ?? $row['notificar_shopee_retornado'] ?? null),
            'data_prevista_exame_retorno' => $parseDate($row['data_prevista_exame_de_retorno_ao_trabalho'] ?? $row['data_prevista_exame'] ?? null),
            'clinica' => $cleanValue($row['clinica'] ?? null),
            'afastamento_inicial' => $cleanValue($row['afastamento_inicial'] ?? null),
            'data_recebimento_aso' => $parseDate($row['data_de_recebimento_do_aso'] ?? $row['data_recebimento_aso'] ?? null),
            'data_envio_aso_shopee' => $parseDate($row['data_envio_aso_shopee'] ?? null),
            'status_atual' => $cleanValue($row['status_atual'] ?? null),
            'data_retorno_atividades' => $parseDate($row['data_do_retorno_das_atividades'] ?? $row['data_retorno_atividades'] ?? null),
            'periodo_restricao' => $cleanValue($row['periodo_de_restricao'] ?? $row['periodo_restricao'] ?? null),
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

    public function onError(Throwable $e)
    {
        \Log::error("Erro durante importação na linha {$this->rowNumber}: " . $e->getMessage());
    }
    
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            \Log::error("Falha na linha {$failure->row()}: " . implode(', ', $failure->errors()));
        }
    }
}