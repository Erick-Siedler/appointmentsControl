<?php

namespace Tests\Feature;

use App\Exports\AppointmentsExport;
use App\Models\Appointment;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class AppointmentsExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_excel_has_official_columns_and_numeric_decimal_duration(): void
    {
        $project = Project::factory()->create(['name' => 'Portal Brê']);
        Appointment::factory()->for($project)->create([
            'date' => '2026-10-03',
            'duration_minutes' => 75,
            'project_task' => 'Ajuste 42',
            'entry_type' => 'overtime',
            'internal_description' => 'Revisão final',
        ]);

        $content = ExcelFacade::raw(new AppointmentsExport('2026-10-03'), Excel::XLSX);
        $path = tempnam(sys_get_temp_dir(), 'appointments-export-');
        file_put_contents($path, $content);
        $sheet = IOFactory::load($path)->getActiveSheet();
        unlink($path);

        $this->assertSame([
            'Recurso Reservável', 'Projeto', 'Tarefa do Projeto', 'Início', 'Fim',
            'Duração', 'Tipo de Entrada', 'Descrição interna', 'Proprietário',
        ], $sheet->rangeToArray('A1:I1')[0]);
        $this->assertSame([
            'Brenda Siedler', 'Portal Brê', 'Ajuste 42', '03/10/2026', '03/10/2026',
            '1.25', 'Horas Extras', 'Revisão final', 'Brenda Siedler',
        ], $sheet->rangeToArray('A2:I2')[0]);
        $this->assertSame(1.25, $sheet->getCell('F2')->getValue());
    }
}
