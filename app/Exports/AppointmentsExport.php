<?php

namespace App\Exports;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AppointmentsExport implements FromQuery, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private readonly string $date) {}

    public function query(): Builder
    {
        return Appointment::query()
            ->with('project:id,name')
            ->whereDate('date', $this->date)
            ->oldest('created_at')
            ->oldest('id');
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Recurso Reservável',
            'Projeto',
            'Tarefa do Projeto',
            'Início',
            'Fim',
            'Duração',
            'Tipo de Entrada',
            'Descrição interna',
            'Proprietário',
        ];
    }

    /**
     * @param  Appointment  $appointment
     * @return array<int, string|float>
     */
    public function map(mixed $appointment): array
    {
        return [
            'Brenda Siedler',
            $this->safeText($appointment->project->name),
            $this->safeText($appointment->project_task),
            $appointment->date->format('d/m/Y'),
            $appointment->date->format('d/m/Y'),
            round($appointment->duration_minutes / 60, 2),
            $appointment->entry_type === 'overtime' ? 'Horas Extras' : 'Trabalho',
            $this->safeText($appointment->internal_description),
            'Brenda Siedler',
        ];
    }

    /**
     * @return array<string, float|int>
     */
    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 26,
            'C' => 28,
            'D' => 15,
            'E' => 15,
            'F' => 13,
            'G' => 20,
            'H' => 48,
            'I' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '1D4ED8'],
                ],
            ],
            'A:I' => [
                'alignment' => [
                    'vertical' => 'top',
                    'wrapText' => true,
                ],
            ],
        ];
    }

    private function safeText(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return preg_match('/^[=+\-@\t\r]/u', $value) === 1 ? "'{$value}" : $value;
    }
}
