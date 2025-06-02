<?php

namespace App\Exports;

use App\Models\Agendamento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AgendamentosTemplateExport implements WithHeadings, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            'ID',
            'CNPJ Unidade',
            'Nome Unidade',
            'Cidade de Atendimento',
            'Estado de Atendimento',
            'Data de Nascimento',
            'Data do Exame',
            'Horário do Exame',
            'Nome do Funcionário',
            'Contato WhatsApp',
            'RG',
            'CPF',
            'Data de Admissão',
            'Função',
            'Setor',
            'Tipo de Exame',
            'Status',
            'Usuário Responsável',
            'Data da Solicitação',
            'Nome do Solicitante',
            'E-mail do Solicitante',
            'WhatsApp do Solicitante',
            'Data da Devolutiva',
            'Clínica Agendada',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Aplica estilo ao cabeçalho
        $sheet->getStyle('A1:X1')->applyFromArray([
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
        $sheet->getStyle('A2:X' . ($sheet->getHighestRow()))
            ->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2'], // Cor de fundo cinza claro
                ],
            ]);
    
        // Aplica estilo às linhas pares (branco)
        for ($i = 2; $i <= $sheet->getHighestRow(); $i += 2) {
            $sheet->getStyle('A' . $i . ':X' . $i)
                ->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFFFF'], // Cor de fundo branco
                    ],
                ]);
        }
    
        // Aplica bordas a todas as células de A até AA
        $sheet->getStyle('A1:X' . $sheet->getHighestRow())
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Borda fina
                        'color' => ['rgb' => '000000'], // Cor da borda (preto)
                    ],
                ],
            ]);
    
        // Centraliza o texto em todas as células de A até AA
        $sheet->getStyle('A1:X' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }


    public function columnWidths(): array
    {
        return [
            'A' => 5, // Empresa
            'B' => 25, // CNPJ da Unidade
            'C' => 20, // Nome da Unidade
            'D' => 30, // Cidade de Atendimento
            'E' => 20,  // Estado de Atendimento
            'F' => 15, // Data do Exame
            'G' => 15, // Horário do Exame
            'H' => 20, // Nome do Funcionário
            'I' => 15, // Contato WhatsApp
            'J' => 20, // RG
            'K' => 20, // CPF
            'L' => 15, // Data de Nascimento
            'M' => 15, // Data de Admissão
            'N' => 20, // Função
            'O' => 20, // Setor
            'P' => 15, // Tipo de Exame
            'Q' => 15, // Status
            'R' => 20, // SLA
            'S' => 20, // Usuário Responsável
            'T' => 20, // Data da Solicitação
            'U' => 20, // Origem do Agendamento
            'V' => 25, // Nome do Solicitante
            'W' => 15, // E-mail do Solicitante
            'X' => 30, // WhatsApp do Solicitante
        ];
    }
    
    
}