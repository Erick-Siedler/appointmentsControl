<?php

namespace App\Http\Controllers;

use App\Exports\AppointmentsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AppointmentExportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): BinaryFileResponse
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ], [
            'date.date_format' => 'A data selecionada é inválida.',
        ]);

        $date = $validated['date'] ?? today()->toDateString();

        return Excel::download(
            new AppointmentsExport($date),
            "apontamentos-{$date}.xlsx",
        );
    }
}
