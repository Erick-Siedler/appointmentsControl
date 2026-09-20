<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveAppointmentRequest;
use App\Models\Appointment;
use App\Models\Project;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ], [
            'date.date_format' => 'A data selecionada é inválida.',
        ]);

        $selectedDate = CarbonImmutable::parse($validated['date'] ?? today()->toDateString())->startOfDay();
        $appointments = Appointment::query()
            ->with('project:id,name')
            ->whereDate('date', $selectedDate->toDateString())
            ->oldest('created_at')
            ->oldest('id')
            ->get();

        $workMinutes = $appointments->where('entry_type', 'work')->sum('duration_minutes');
        $overtimeMinutes = $appointments->where('entry_type', 'overtime')->sum('duration_minutes');

        return view('appointments.index', [
            'appointments' => $appointments,
            'projects' => Project::query()->orderBy('name')->get(['id', 'name']),
            'selectedDate' => $selectedDate,
            'previousDate' => $selectedDate->subDay()->toDateString(),
            'nextDate' => $selectedDate->addDay()->toDateString(),
            'summary' => [
                'count' => $appointments->count(),
                'work' => Appointment::formatMinutes($workMinutes),
                'overtime' => Appointment::formatMinutes($overtimeMinutes),
                'total' => Appointment::formatMinutes($workMinutes + $overtimeMinutes),
            ],
        ]);
    }

    public function store(SaveAppointmentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $project = $this->resolveProject($validated);

            Appointment::create($this->appointmentData($validated, $project));
        });

        return redirect()
            ->route('appointments.index', ['date' => $validated['date']])
            ->with('success', 'Apontamento criado com sucesso.');
    }

    public function update(SaveAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $appointment): void {
            $project = $this->resolveProject($validated);

            $appointment->update($this->appointmentData($validated, $project));
        });

        return redirect()
            ->route('appointments.index', ['date' => $validated['date']])
            ->with('success', 'Apontamento atualizado com sucesso.');
    }

    public function destroy(Request $request, Appointment $appointment): RedirectResponse
    {
        $selectedDate = $request->string('date', $appointment->date->toDateString())->toString();

        $appointment->delete();

        return redirect()
            ->route('appointments.index', ['date' => $selectedDate])
            ->with('success', 'Apontamento excluído com sucesso.');
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function resolveProject(array $validated): Project
    {
        if (! empty($validated['new_project_name'])) {
            $name = $validated['new_project_name'];
            $normalizedName = Str::lower($name);

            return Project::query()
                ->whereRaw('LOWER(name) = ?', [$normalizedName])
                ->first() ?? Project::create(['name' => $name]);
        }

        return Project::query()->findOrFail($validated['project_id']);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function appointmentData(array $validated, Project $project): array
    {
        [$hours, $minutes] = array_map('intval', explode(':', $validated['duration']));

        return [
            'date' => $validated['date'],
            'duration_minutes' => ($hours * 60) + $minutes,
            'project_id' => $project->id,
            'project_task' => $validated['project_task'] ?? null,
            'occurrence' => $validated['occurrence'] ?? null,
            'internal_description' => $validated['internal_description'] ?? null,
            'entry_type' => $validated['entry_type'],
            'owner' => $validated['owner'] ?? null,
        ];
    }
}
