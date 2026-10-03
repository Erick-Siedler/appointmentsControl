<?php

namespace App\Http\Controllers;

use App\Models\DailyNote;
use App\Models\PendingTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PendingTaskController extends Controller
{
    public function store(Request $request, DailyNote $dailyNote): RedirectResponse
    {
        $this->authorizeAuthor($request, $dailyNote);
        $dailyNote->pendingTasks()->create($this->validatedTask($request));

        return $this->backToNote($dailyNote, 'Pendência adicionada.');
    }

    public function update(Request $request, PendingTask $pendingTask): RedirectResponse
    {
        $dailyNote = $pendingTask->dailyNote;
        $this->authorizeAuthor($request, $dailyNote);
        $pendingTask->update($this->validatedTask($request));

        return $this->backToNote($dailyNote, 'Pendência atualizada.');
    }

    public function move(Request $request, PendingTask $pendingTask): RedirectResponse
    {
        $dailyNote = $pendingTask->dailyNote;
        $this->authorizeAuthor($request, $dailyNote);
        $validated = $request->validateWithBag('tasks', ['status' => ['required', Rule::in(array_keys(PendingTask::STATUSES))]]);
        $pendingTask->update(['status' => $validated['status']]);

        return $this->backToNote($dailyNote, 'Status atualizado.');
    }

    public function destroy(Request $request, PendingTask $pendingTask): RedirectResponse
    {
        $dailyNote = $pendingTask->dailyNote;
        $this->authorizeAuthor($request, $dailyNote);
        $pendingTask->delete();

        return $this->backToNote($dailyNote, 'Pendência excluída.');
    }

    /** @return array<string, mixed> */
    private function validatedTask(Request $request): array
    {
        return $request->validateWithBag('tasks', [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(array_keys(PendingTask::STATUSES))],
            'priority' => ['required', Rule::in(array_keys(PendingTask::PRIORITIES))],
            'due_date' => ['nullable', 'date_format:Y-m-d'],
            'assignee' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function authorizeAuthor(Request $request, DailyNote $dailyNote): void
    {
        abort_unless($request->session()->get('note_author_token') === $dailyNote->author_token, 403);
    }

    private function backToNote(DailyNote $dailyNote, string $message): RedirectResponse
    {
        return redirect()->route('appointments.index', ['date' => $dailyNote->date->toDateString(), 'notes' => $dailyNote->id])
            ->with('success', $message);
    }
}
