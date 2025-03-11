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
    public function collection()
    {
        // Retorna todos os dados do modelo Agendamento
        return Agendamento::with(['empresa', 'unidade', 'user']) // Carrega os relacionamentos
            ->get()
            ->map(function ($agendamento) {
                return [
                    'id' => $agendamento->id,
                    'empresa' => $agendamento->empresa ? $agendamento->empresa->nome : 'N/A',
                    'unidade' => $agendamento->unidade ? $agendamento->unidade->nome : 'N/A',
                    'cidade_atendimento' => $agendamento->cidade_atendimento,
                    'estado_atendimento' => $agendamento->estado_atendimento,
                    'data_exame' => $agendamento->data_exame,
                    'horario_exame' => $agendamento->horario_exame,
                    'nome_funcionario' => $agendamento->nome_funcionario,
                    'contato_whatsapp' => $agendamento->contato_whatsapp,
                    'doc_identificacao_rg' => $agendamento->doc_identificacao_rg,
                    'doc_identificacao_cpf' => $agendamento->doc_identificacao_cpf,
                    'data_nascimento' => $agendamento->data_nascimento,
                    'data_admissao' => $agendamento->data_admissao,
                    'funcao' => $agendamento->funcao,
                    'setor' => $agendamento->setor,
                    'tipo_exame' => $agendamento->tipo_exame,
                    'status' => $agendamento->status,
                    'sla' => $agendamento->sla,
                    'user' => $agendamento->user ? $agendamento->user->name : 'N/A',
                    'data_solicitacao' => $agendamento->data_solicitacao,
                    'nome_solicitante' => $agendamento->nome_solicitante,
                    'email_solicitante' => $agendamento->email_solicitante,
                    'whatsapp_solicitante' => $agendamento->whatsapp_solicitante,
                    'data_devolutiva' => $agendamento->data_devolutiva,
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