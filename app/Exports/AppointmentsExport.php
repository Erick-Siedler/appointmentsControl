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
            'Data',
            'Duração',
            'Projeto',
            'Tarefa do projeto',
            'Ocorrência',
            'Descrição interna',
            'Tipo de apontamento',
            'Responsável',
        ];
    }

    /**
     * @param  Appointment  $appointment
     * @return array<int, string>
     */
    public function map(mixed $appointment): array
    {
        return [
            $appointment->date->format('d/m/Y'),
            $appointment->formattedDuration(),
            $this->safeText($appointment->project->name),
            $this->safeText($appointment->project_task),
            $this->safeText($appointment->occurrence),
            $this->safeText($appointment->internal_description),
            $appointment->entryTypeLabel(),
            $this->safeText($appointment->owner),
        ];
    }

    /**
     * @return array<string, float|int>
     */
    public function columnWidths(): array
    {
        return [
            'A' => 13,
            'B' => 11,
            'C' => 24,
            'D' => 28,
            'E' => 32,
            'F' => 48,
            'G' => 22,
            'H' => 24,
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
            'A:H' => [
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
