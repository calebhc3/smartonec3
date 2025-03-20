<?php

namespace App\Exports;

use App\Models\Agendamento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AgendamentosExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        \Log::info('Filtros recebidos:', $this->filters); // Log dos filtros
    
        $query = Agendamento::query();
    
        // Filtro de busca
        if (!empty($this->filters['buscar']['search'])) {
            $searchTerm = $this->filters['buscar']['search'];
            $query->where(function ($query) use ($searchTerm) {
                $query->where('nome_funcionario', 'like', "%{$searchTerm}%")
                      ->orWhere('doc_identificacao_cpf', 'like', "%{$searchTerm}%");
            });
        }
    
        // Filtro de tipo de exame
        if (!empty($this->filters['tipo_exame']['value'])) {
            $query->where('tipo_exame', $this->filters['tipo_exame']['value']);
        }
    
        // Filtro de ano
        if (!empty($this->filters['ano_registro']['ano'])) {
            $query->whereYear('data_exame', $this->filters['ano_registro']['ano']);
        }
    
        // Filtro de mês
        if (!empty($this->filters['mes_registro']['mes'])) {
            $query->whereMonth('data_exame', $this->filters['mes_registro']['mes']);
        }
    
        // Filtro de empresa
        if (!empty($this->filters['empresa_id']['value'])) {
            $query->where('empresa_id', $this->filters['empresa_id']['value']);
        }
    
        // Filtro de status
        if (!empty($this->filters['status']['value'])) {
            $query->where('status', $this->filters['status']['value']);
        }
    
        // Filtro de data do exame
        if (!empty($this->filters['data_exame']['data_exame'])) {
            $query->whereDate('data_exame', $this->filters['data_exame']['data_exame']);
        }
    
        // Filtro de SLA
        if (!empty($this->filters['sla']['value'])) {
            $query->where('sla', $this->filters['sla']['value']);
        }
    
        // Retorna os dados filtrados com os relacionamentos
        $resultados = $query->with(['empresa', 'user'])->get();
        \Log::info('Resultados da query:', $resultados->toArray()); // Log dos resultados
    
        return $resultados->map(function ($agendamento) {
            return [
                'id' => $agendamento->id,
                'empresa' => $agendamento->empresa ? $agendamento->empresa->nome : 'N/A',
                'cidade_atendimento' => $agendamento->cidade_atendimento,
                'estado_atendimento' => $agendamento->estado_atendimento,
                'data_exame' => $agendamento->data_exame ? \Carbon\Carbon::parse($agendamento->data_exame)->format('d/m/Y') : 'N/A',
                'horario_exame' => $agendamento->horario_exame,
                'nome_funcionario' => $agendamento->nome_funcionario,
                'contato_whatsapp' => $agendamento->contato_whatsapp,
                'doc_identificacao_rg' => $agendamento->doc_identificacao_rg,
                'doc_identificacao_cpf' => $agendamento->doc_identificacao_cpf,
                'data_exame' => $agendamento->data_exame ? \Carbon\Carbon::parse($agendamento->data_exame)->format('d/m/Y') : 'N/A', // Verifica se não é nulo
                'data_admissao' => $agendamento->data_admissao ? \Carbon\Carbon::parse($agendamento->data_admissao)->format('d/m/Y') : 'N/A',
                'funcao' => $agendamento->funcao,
                'setor' => $agendamento->setor,
                'tipo_exame' => $agendamento->tipo_exame,
                'status' => $agendamento->status,
                'sla' => $agendamento->sla,
                'user' => $agendamento->user ? $agendamento->user->name : 'N/A',
                'data_solicitacao' => $agendamento->data_solicitacao ? \Carbon\Carbon::parse($agendamento->data_solicitacao)->format('d/m/Y') : 'N/A',
                'nome_solicitante' => $agendamento->nome_solicitante,
                'email_solicitante' => $agendamento->email_solicitante,
                'whatsapp_solicitante' => $agendamento->whatsapp_solicitante,
                'data_devolutiva' => $agendamento->data_devolutiva ? \Carbon\Carbon::parse($agendamento->data_devolutiva)->format('d/m/Y') : 'N/A',
                'clinica_agendada' => $agendamento->clinica_agendada,
                'comparecimento' => $agendamento->comparecimento,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Empresa',
            'Unidade',
            'Cidade de Atendimento',
            'Estado de Atendimento',
            'Data do Exame',
            'Horário do Exame',
            'Nome do Funcionário',
            'Contato WhatsApp',
            'RG',
            'CPF',
            'Data de Nascimento',
            'Data de Admissão',
            'Função',
            'Setor',
            'Tipo de Exame',
            'Status',
            'SLA',
            'Usuário Responsável',
            'Data da Solicitação',
            'Nome do Solicitante',
            'E-mail do Solicitante',
            'WhatsApp do Solicitante',
            'Data da Devolutiva',
            'Clínica Agendada',
            'Comparecimento',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Aplica estilo ao cabeçalho
        $sheet->getStyle('A1:Z1')->applyFromArray([
            'font' => [
                'bold' => true, // Negrito
                'color' => ['rgb' => 'FFFFFF'], // Cor da fonte (branco)
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'], // Cor de fundo (azul)
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Borda fina
                    'color' => ['rgb' => '000000'], // Cor da borda (preto)
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Alinhamento centralizado
            ],
        ]);

        // Aplica estilo às linhas intercaladas (zebra stripes)
        $sheet->getStyle('A2:Z' . ($sheet->getHighestRow()))
            ->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2'], // Cor de fundo cinza claro
                ],
            ]);

        // Aplica estilo às linhas pares (branco)
        for ($i = 2; $i <= $sheet->getHighestRow(); $i += 2) {
            $sheet->getStyle('A' . $i . ':Z' . $i)
                ->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFFFF'], // Cor de fundo branco
                    ],
                ]);
        }

        // Aplica bordas a todas as células
        $sheet->getStyle('A1:Z' . $sheet->getHighestRow())
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Borda fina
                        'color' => ['rgb' => '000000'], // Cor da borda (preto)
                    ],
                ],
            ]);

        // Centraliza o texto em todas as células
        $sheet->getStyle('A1:Z' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  // ID
            'B' => 20, // Empresa
            'C' => 20, // Unidade
            'D' => 20, // Cidade de Atendimento
            'E' => 5, // Estado de Atendimento
            'F' => 15, // Data do Exame
            'G' => 15, // Horário do Exame
            'H' => 20, // Nome do Funcionário
            'I' => 15, // Contato WhatsApp
            'J' => 15, // RG
            'K' => 15, // CPF
            'L' => 15, // Data de Nascimento
            'M' => 15, // Data de Admissão
            'N' => 20, // Função
            'O' => 15, // Setor
            'P' => 15, // Tipo de Exame
            'Q' => 15, // Status
            'R' => 20, // SLA
            'S' => 20, // Usuário Responsável
            'T' => 20, // Data da Solicitação
            'U' => 20, // Nome do Solicitante
            'V' => 25, // E-mail do Solicitante
            'W' => 20, // WhatsApp do Solicitante
            'X' => 20, // Data da Devolutiva
            'Y' => 25, // Clínica Agendada
            'Z' => 15, // Comparecimento
        ];
    }
}